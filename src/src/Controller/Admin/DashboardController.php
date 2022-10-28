<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Vote;
use App\Entity\Question;
use Symfony\Component\Security\Core\Security;
use App\Controller\Admin\PostCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
// granted access to the admin dashboard only for users with the ROLE_ADMIN role

class DashboardController extends AbstractDashboardController
{


    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        
        // si le user est admin, on le redirige vers la page admin sinon vers home
        if ($this->isGranted('ROLE_ADMIN')) {
            $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
            return $this->redirect($adminUrlGenerator->setController(PostCrudController::class)->generateUrl());
        } else {
            return $this->redirect($this->generateUrl('app_home'));
        }

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Goofy CORP');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Gestion des posts');
        yield MenuItem::linkToCrud('Post', 'fas fa-list', Post::class);
        yield MenuItem::linkToCrud('Question', 'fas fa-list', Question::class);
        yield MenuItem::linkToCrud('Tag', 'fas fa-list', Tag::class);
        yield MenuItem::linkToCrud('Vote', 'fas fa-list', Vote::class);
        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
    }
}
