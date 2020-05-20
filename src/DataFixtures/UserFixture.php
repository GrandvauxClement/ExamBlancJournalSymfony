<?php
    namespace App\DataFixtures;

    use App\Entity\User;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;


    class UserFixture extends Fixture
    {
        private $encoder;

        public function __construct(UserPasswordEncoderInterface $encoder)
        {
            $this->encoder = $encoder;
        }

        // ...
        public function load(ObjectManager $manager)
        {
            $user = new User();
            $user->setEmail('admin@admin.com');

            $password = $this->encoder->encodePassword($user, 'adminadmin');
            $user->setPassword($password);

            $user->setNom('Admin');

            $manager->persist($user);
            $manager->flush();
        }
    }

