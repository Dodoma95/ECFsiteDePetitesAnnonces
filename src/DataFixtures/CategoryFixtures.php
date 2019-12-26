<?php


namespace App\DataFixtures;


use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    private function createCategory($name, $order){
        $category = new Category();
        $category->setName($name);
        $this->addReference("category_$order", $category);
        return $category;
    }
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createCategory("Véhicule", 1));
        $manager->persist($this->createCategory("Vacance", 2));
        $manager->persist($this->createCategory("Mode", 3));
        $manager->persist($this->createCategory("Immobilier", 4));
        $manager->persist($this->createCategory("Loisir", 5));
        $manager->persist($this->createCategory("Multimédia", 6));
        $manager->persist($this->createCategory("Service", 7));
        $manager->flush();
    }
}