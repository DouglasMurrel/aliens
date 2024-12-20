<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Entity\RecoverPasswordRequest;
use App\Entity\User;
use App\Http\Request\PasswordRecoverRequest;
use App\Http\Request\PasswordRecoverSecondRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[Route('/recover-password', name: 'recover-password-', methods: ['POST','OPTIONS'])]
class RecoverPasswordController extends AbstractController
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        MailerInterface $mailer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger
    )
    {
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
    }
    
    
    #[Route('/create', name: 'create', methods: ['POST','OPTIONS'])]
    public function createRequest(Request $request): JsonResponse
    {
        try {
            $recoverRequest = $this->decodeRequest($request);
        } catch (\Exception $e) {
            return $this->json(['error'=>'Неверный запрос'], Response::HTTP_UNAUTHORIZED);
        }
        $violations = $this->validator->validate($recoverRequest);
        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['error'=> implode(',',$errors)], Response::HTTP_UNAUTHORIZED);
        }
        
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$recoverRequest->getEmail()]);
        if (!$user) {
           return $this->json(['error'=>'Пользователь с таким email не найден'], Response::HTTP_UNAUTHORIZED); 
        }

        $recoverEntity = new RecoverPasswordRequest();
        $recoverEntity->setEmail($recoverRequest->getEmail());
        $this->em->persist($recoverEntity);
        $this->em->flush();

        $email = (new Email())
            ->from('Битва школ <murrel@yandex.ru>')
            ->to($recoverRequest->getEmail())
            ->subject('Восстанлвление пароля')
            ->html("<div style='margin-bottom:5px;'>Добрый день!</div>"
                    . "<div>Кто-то запросил восстановление вашего пароля.</div>"
                    . "<div>Если это были вы, пройдите по "
                    . "<a style='border-bottom:1px solid;color: blue;' target='_blank' href='"
                    . $this->getParameter('recoverPasswordUrl') . "?email=" . $recoverEntity->getEmail() . "&code=" . $recoverEntity->getCode()
                    ."'>ссылке</a>.</div"
                    . "<div>Если нет, просто игнорируйте это письмо</div>");

        $this->mailer->send($email);
        
        return $this->json([], Response::HTTP_OK);
    }
    
    #[Route('/check', name: 'check', methods: ['POST','OPTIONS'])]
    public function checkRequest(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            return $this->json(['error'=>'Неверный запрос'], Response::HTTP_UNAUTHORIZED);
        }
        $email = $data['email'];
        $code = $data['code'];
        $password = $data['password'];

        $passwordRequest = new PasswordRecoverSecondRequest();
        $passwordRequest->setPassword($password);
        $violations = $this->validator->validate($passwordRequest);
        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['error'=> implode(',',$errors)], Response::HTTP_UNAUTHORIZED);
        }
        
        $this->logger->info($email);
        try {
            $recoverEntity = $this->em->createQueryBuilder()
                ->select('r')
                ->from(RecoverPasswordRequest::class, 'r')
                ->where('r.email=:email')
                ->andWhere('r.code=:code')
                ->andWhere('r.validUntil>=:dt')
                ->setParameter('email', $email)
                ->setParameter('code', $code)
                ->setParameter('dt', new \DateTime())
                ->getQuery()
                ->getOneOrNullResult()
                ;
            if (!$recoverEntity) {
                return $this->json(['error'=>'Код недействителен'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return $this->json(['error'=>'Код не уникален'], Response::HTTP_UNAUTHORIZED);
        }
        
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if (!$user) {
            return $this->json(['error'=>'Пользователь с таким email не найден'], Response::HTTP_UNAUTHORIZED);
        }
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();
        
        return $this->json([], Response::HTTP_OK);            
    }
    
    private function decodeRequest(Request $request): PasswordRecoverRequest
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        
        $data = $request->getContent();

        $passwordRecoverRequest = $serializer->deserialize($data, PasswordRecoverRequest::class, 'json');
        return $passwordRecoverRequest;
    }
}
