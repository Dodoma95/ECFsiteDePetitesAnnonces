<?php


namespace App\DataFixtures;


use App\Entity\Ad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AdFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $numberOfUsers = 4;
        $numberOfCategory = 7;

        for($i=1; $i <= 100; $i++){
            $ad = new Ad();
            $ad ->setTitle($faker->sentence(8))
                ->setContent($faker->text(mt_rand(300, 1500)))
                ->setCreatedAt($faker->dateTimeThisDecade)
                ->setPhoto($faker->image('public/storage/images',400,300, null, false))
                ->setUser($this->getReference("user_". mt_rand(1, $numberOfUsers)))
                ->setCategory($this->getReference("category_". mt_rand(1, $numberOfCategory)));

            $this->addReference("ad_$i", $ad);
            $manager->persist($ad);
        }
        $manager->flush();
    }
}