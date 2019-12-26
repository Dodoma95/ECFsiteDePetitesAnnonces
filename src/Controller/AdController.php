<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Category;
use App\Entity\User;
use App\Form\AdType;
use App\Repository\AdRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ad")
 * Class AdController
 * @package App\Controller
 */
class AdController extends AbstractController
{
    /**
     * @Route("/", name="ad-list")
     * @param AdRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(AdRepository $repository, PaginatorInterface $paginator, Request $request)
    {

        $adList = $paginator->paginate(
            $repository->getAllAds(),
            $request->query->getInt('page', 1),
            10
        );

        dump($adList);

        $params = $this->getTwigParametersWithAside(
            [
                'adList' => $adList, 'pagetitle' => ''
            ]
        );
        return $this->render('ad/index.html.twig', $params);
    }

    /**
     * @Route("/by-category/{id}", name="ad-by-category")
     * @param Category $category
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param AdRepository $repository
     * @return Response
     */
    public function showByCategory(Category $category, Request $request, PaginatorInterface $paginator, AdRepository $repository){
        $adList = $paginator->paginate(
            $repository->getAllByCategory($category),
            $request->query->getInt('page', 1),
            10
        );

        $params = $this->getTwigParametersWithAside(
            ['adList' => $adList, 'pageTitle' => "de la catégorie : ". $category->getName()]
        );

        return $this->render('ad/index.html.twig', $params);
    }

    /**
     * @param $data
     * @return array
     */
    private function getTwigParametersWithAside($data){
        $asideData =[
            'categoryList' => $this->getDoctrine()
                ->getRepository(Category::class)
                ->findAll()
        ];

        return array_merge($data, $asideData);
    }

    /**
     * @Route("/ad/new", name="ad-create")
     * @Route("/ad/edit/{id}", name="ad-edit")
     * @param Request $request
     * @param null $id
     * @return RedirectResponse|Response
     */
    public function createOrEditAd(Request $request, $id=null){

        // Création du formulaire
        if($id == null){
            $ad = new Ad();
        } else {
            $ad = $this   ->getDoctrine()
                ->getRepository(Ad::class)
                ->find($id);
        }

        $form = $this->createForm(AdType::class, $ad);

        //recupere les données postées et l'injecte dans le formulaire
        //l'objet associé donc book est aussi hydraté
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute("ad-list");
        }

        return $this->render("ad/new.html.twig", [
            "adForm" => $form->createView()
        ]);
    }

    /**
     * @Route("/ad/delete/{id}", name="ad-delete")
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAd($id){

        $repository = $this->getDoctrine()->getRepository(Ad::class);
        $ad = $repository->find($id);

        $entityManager = $this->getDoctrine()->getManager();

        if($ad){
            $entityManager->remove($ad);

            $entityManager->flush();
        }

        return $this->redirectToRoute("ad-list");
    }

    /**
     * @Route("/ad/{id}", name="ad-details")
     * @param Ad $ad
     * @return Response
     */
    public function details(Ad $ad){

        return $this->render('ad/details.html.twig', [
            'ad' => $ad,
        ]);
    }

    /**
     * @Route("/new", name="ad-new")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param null $id
     * @return RedirectResponse|Response
     */
    public function addOrEdit(Request $request, $id=null){
        $ad = new Ad();
        $ad->setUser($this->getUser());
        //Equivalent de @IsGranted dans les annotations

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ad);
            $em->flush();

            $this->addFlash('success', "Votre annonce a été ajouté");

            return $this->redirectToRoute('ad-list');
        }

        return $this->render('ad/form.html.twig', [
            'adForm' => $form->createView()
        ]);
    }
}
