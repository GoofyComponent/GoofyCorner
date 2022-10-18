<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         IdField::new('id')->hideOnForm(),
    //         EmailField::new('email'),
    //         TextField::new('firstname'),
    //         TextField::new('lastname'),
    //         ChoiceField::new('roles')->setChoices([
    //             'Admin' => 'ROLE_ADMIN',
    //             'User' => 'ROLE_USER',
    //         ])->allowMultipleChoices(),
    //         TextField::new('adresse'),
    //         BooleanField::new('isVerified'),
    //     ];
    // }
}
