<?php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *[
     * @param string $slug The slugger
     * @Route("wild/program/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_program")
     * @return Response
     */
    public function showProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = str_replace('-', ' ', ucwords(trim(strip_tags($slug)), '-'));
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'slug' => $slug,

        ]);
    }


    /**
     * @param string $categoryName
     * @return Response
     * @Route("wild/category/{categoryName}", name="wild_category")
     */
    public function showByCategory (string $categoryName): Response
    {
        $category= $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name'=>($categoryName)]);
        $programs=$this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category'=>$category], ['id'=>'DESC'],3);
        return $this->render('wild/category.html.twig', ['programs'=> $programs]);
    }


    /**
     * Getting a program with a formatted slug for title and can see all episode from season
     *
     * @param int $id The id season
     * @Route("/wild/season/{id}",  name="wild_season")
     * @return Response
     */
    public function showBySeason (?int $id): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::Class)
            ->findOneBy(['id' => $id]);
        $program = $season->getProgram();
        $episode = $season->getEpisodes();
        return $this->render('wild/season.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }


    /**
     * @param Episode $episode
     * @return Response
     * @Route("wild/episode/{id}", name="wild_episode")
     */
    public function showEpisode(Episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
        ]);
    }
}

