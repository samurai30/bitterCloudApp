<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@gold.com',
            'password' => 'admin1234',
            'fullName' => 'admin Suyog',
            'roles' => [User::ROLE_ADMIN]
        ],
    ];

    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {     $this->loadusers($manager);
        $this->loadMicroPost($manager);

    }
    private function loadMicroPost(ObjectManager $manager){
        for ($i = 0; $i <2; $i++){
            $microPost = new MicroPost();
            $microPost->setText(self::POST_TEXT[rand(0,count(self::POST_TEXT)-1)]);
            $date = new \DateTime();
            $date->modify('-'.rand(0,10).'day');
            $microPost->setTime($date);
            $microPost->setUser($this->getReference(
                self::USERS[rand(0, count(self::USERS)-1)]['username']
            ));
            $manager->persist($microPost);
        }

        $manager->flush();
    }
    private function loadusers(ObjectManager $manager){
        foreach (self::USERS as $userData){
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullName($userData['fullName']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->encoder->encodePassword($user, $userData['password']));
            $user->setRoles($userData['roles']);
            $this->addReference($userData['username'],$user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
