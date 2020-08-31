<?php

namespace App\Controller;

use App\Entity\FavoriteNews;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{

	// index controller pour la page HOME donc l'index. Affichage d'un menu basique

    /**
    * @Route("/", name="home")
    */
    public function index(HttpClientInterface $httpClient)
    {
        return $this->render('news/index.html.twig');
	}
	
	// Sources controller pour la page sources. Affichage de toute les sources

    /**
     * @Route("/sources", name="news")
     */
    public function sources(HttpClientInterface $httpClient)
    {
		// Récupération des sources via l'apinews en utilisant HttpClientInterface
        $response = $httpClient->request('GET','https://newsapi.org/v2/sources?apiKey=bf7e03ddccc2499cb39aad8226f487f3',[
            'query' => [
                'sort' => 'created'
            ]
        ]);

		// On décode le code JSON pour qu'il entre dans un array

        $content = $response->getContent();
        $data = json_decode($content, true);

		// On envoie les données à la vue

        return $this->render('news/source.html.twig', [
            'news' => $data['sources'],
        ]);
    }

	// Show controller pour la page des articles. Affichage de toute les articles d'une source précide (ici {id})

    /**
     * @Route("/source/{id}/", name="show")
     */
    public function show($id, HttpClientInterface $httpClient, Request $request, EntityManagerInterface $manager)
    {
		// Récupération des sources via l'apinews en utilisant HttpClientInterface

		$response = $httpClient->request('GET','https://newsapi.org/v2/top-headlines?sources='.$id.'&apiKey=bf7e03ddccc2499cb39aad8226f487f3');
		// On décode le code JSON pour qu'il entre dans un array
		$content = $response->getContent();
        $data = json_decode($content, true);

		// Si l'on reçoit la requête POST
        if ($request->request->count() > 0)
        {
			// On récupère l'id de notre utilisateur
			$userid = $this->getUser()->getId();
			// On crée un favoris de l'article sélectionné
            $favorite = new FavoriteNews();
            $favorite->setUserId($userid)
                     ->setSourceName($request->request->get('sourcename'))
                     ->setAuthor($request->request->get('author'))
                     ->setTitle($request->request->get('title'))
                     ->setDescription($request->request->get('description'))
                     ->setUrl($request->request->get('url'))
                     ->setUrlToImage($request->request->get('urltoimage'))
                     ->setPublishedAt($request->request->get('publishedAt'))
                     ->setContent($request->request->get('content'));
            
            $manager->persist($favorite);
			$manager->flush();
			// On inclu dans la BDD
            
		}
		
        // Code pour disable/active le bouton "Add to favorite" en fonction de l'ajout #non fonctionnel
        $items = array();
        $userid = $this->getUser();
		if($userid != NULL)
		{
        	$favoris = $this->getDoctrine()->getRepository('App:FavoriteNews')->findBy(['User_id' => $userid]);
			foreach ($favoris as $entity)
			{
				$items[] = $entity; //Stock les données dans un tableau
				//var_dump($items);
			}
		}

			 return $this->render('news/show.html.twig', [
        	'news' => $data['articles'],
			'sifav' => $items
        ]);
	}
	
	// Favorite controller pour la page des articles favoris. Affichage les articles favoris de l'user

    /**
     * @Route("/favorite", name="favorite_news")
     */
    public function favorite(Request $request, EntityManagerInterface $manager)
    {
		// On récupère l'id de l'utilisateur

		$userid = $this->getUser()->getId();

		// Et on va chercher les articles qu'il a mit en favoris dans la BDD

        $favoris = $this->getDoctrine()->getRepository('App:FavoriteNews')->findBy(['User_id' => $userid]);

        
		// Si un utilisateur clique sur REMOVE, on recoit une requete, donc on entre ici

        if($request->request->count() > 0)
        {
			// Si l'utilisateur qui surf est le même que celui qui a cliqué on entre

            if ($userid == $request->request->get('userid'))
            {
				// on va récupérer les articles qu'il a mis en favoris selon son id

                $repository = $this->getDoctrine()->getRepository('App:FavoriteNews')->findOneBy(array('id' => $request->request->get('id')), array('User_id' => 'DESC'));
                
                $manager->remove($repository);
				$manager->flush();
				// et on le supprime, puis on force un refresh de la page
                return $this->redirect($request->getUri());
            }


        }

        return $this->render("news/favorite.html.twig",[
            'favoris' => $favoris,
        ]);
    }

	// Apple controller pour la page des articles qui sont en rapport avec Apple

    /**
     * @Route("/apple", name="apple")
     */
    public function apple(HttpClientInterface $httpClient, Request $request, EntityManagerInterface $manager)
    {
        $response = $httpClient->request('GET','http://newsapi.org/v2/everything?q=apple&from=2020-08-25&to=2020-08-25&sortBy=popularity&apiKey=bf7e03ddccc2499cb39aad8226f487f3',[
        'query' => [
        'sort' => 'created']]);

        $content = $response->getContent();
        $data = json_decode($content, true);

		if ($request->request->count() > 0)
        {
            $userid = $this->getUser()->getId();
            $favorite = new FavoriteNews();
            $favorite->setUserId($userid)
                     ->setSourceName($request->request->get('sourcename'))
                     ->setAuthor($request->request->get('author'))
                     ->setTitle($request->request->get('title'))
                     ->setDescription($request->request->get('description'))
                     ->setUrl($request->request->get('url'))
                     ->setUrlToImage($request->request->get('urltoimage'))
                     ->setPublishedAt($request->request->get('publishedAt'))
                     ->setContent($request->request->get('content'));
            
            $manager->persist($favorite);
            $manager->flush();
            
        }
		
        return $this->render('news/apple.html.twig', [
            'news' => $data['articles'],

        ]);
    }

	// Bitcoins controller pour la page des articles qui sont en rapport avec Bitcoins

    /**
     * @Route("/bitcoins", name="bitcoins")
     */
    public function bitcoins(HttpClientInterface $httpClient, Request $request, EntityManagerInterface $manager)
    {
        $response = $httpClient->request('GET','http://newsapi.org/v2/everything?q=bitcoin&from=2020-08-25&to=2020-08-25&sortBy=popularity&apiKey=bf7e03ddccc2499cb39aad8226f487f3        ',[
        'query' => [
        'sort' => 'created']]);

        $content = $response->getContent();
        $data = json_decode($content, true);

		if ($request->request->count() > 0)
        {
            $userid = $this->getUser()->getId();
            $favorite = new FavoriteNews();
            $favorite->setUserId($userid)
                     ->setSourceName($request->request->get('sourcename'))
                     ->setAuthor($request->request->get('author'))
                     ->setTitle($request->request->get('title'))
                     ->setDescription($request->request->get('description'))
                     ->setUrl($request->request->get('url'))
                     ->setUrlToImage($request->request->get('urltoimage'))
                     ->setPublishedAt($request->request->get('publishedAt'))
                     ->setContent($request->request->get('content'));
            
            $manager->persist($favorite);
            $manager->flush();
            
        }

        return $this->render('news/bitcoins.html.twig', [
            'news' => $data['articles'],

        ]);
    }

	// Corona controller pour la page des articles qui sont en rapport avec Corona

    /**
     * @Route("/corona", name="corona")
     */
    public function corona(HttpClientInterface $httpClient, Request $request, EntityManagerInterface $manager)
    {
        $response = $httpClient->request('GET','http://newsapi.org/v2/everything?q=corona&from=2020-08-25&to=2020-08-25&sortBy=popularity&apiKey=bf7e03ddccc2499cb39aad8226f487f3        ',[
        'query' => [
        'sort' => 'created']]);

        $content = $response->getContent();
        $data = json_decode($content, true);

		if ($request->request->count() > 0)
        {
            $userid = $this->getUser()->getId();
            $favorite = new FavoriteNews();
            $favorite->setUserId($userid)
                     ->setSourceName($request->request->get('sourcename'))
                     ->setAuthor($request->request->get('author'))
                     ->setTitle($request->request->get('title'))
                     ->setDescription($request->request->get('description'))
                     ->setUrl($request->request->get('url'))
                     ->setUrlToImage($request->request->get('urltoimage'))
                     ->setPublishedAt($request->request->get('publishedAt'))
                     ->setContent($request->request->get('content'));
            
            $manager->persist($favorite);
            $manager->flush();
            
        }
        return $this->render('news/corona.html.twig', [
            'news' => $data['articles'],

        ]);
    }

}
