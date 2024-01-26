<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(['validation_groups' => ['register']])
                    ->setEntityLabelInPlural('Utilisateurs')
                    ->setPageTitle('new', 'Créer un Utilisateur')
                    ->setPageTitle('edit', function ($entity) {
                        return 'Utilisateur d\'Id: ' . $entity->getId();
                    })
                    ->setPageTitle('detail', function ($entity) {
                        return 'Utilisateur d\'Id: ' . $entity->getId();
                    })
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstName')->setLabel('Prénom'),
            TextField::new('lastName')->setLabel('Nom'),
            EmailField::new('Email'),
            TextField::new('Password')->setFormType(PasswordType::class)
                                      ->setLabel('mot de passe')
                                      ->onlyWhenCreating()
                                      ->setRequired(true),
            TextField::new('password_confirm')->setFormType(PasswordType::class)
                                              ->setLabel('confirmation mot de passe')
                                              ->onlyWhenCreating()
                                              ->setRequired(true),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance,$entityInstance->getPassword()));

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, 'detail')
                       ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                            $action->setIcon('fas fa-trash')
                                   ->displayIf(static function ($entity) {
                                        foreach ($entity->getRoles() as $role) {
                                            return $role != 'ROLE_ADMIN';
                                        }
                                    })
                            ;
                            return $action;
                        })
                       ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                            $action->setIcon('fas fa-trash')
                                   ->displayIf(static function ($entity) {
                                        foreach ($entity->getRoles() as $role) {
                                            return $role != 'ROLE_ADMIN';
                                        }
                                    })
                            ;
                            return $action;
                        })
                       ->update('index', Action::NEW, function (Action $action) {
                            $action->setLabel('Créer un Utilisateur');
                            return $action;
                            })
                        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('firstName')
                       ->add('lastName')
                       ->add('email')
                    //    ->add('active')
                       ->add(ArrayFilter::new('roles')->setChoices(['Admin' => 'ROLE_ADMIN', 'Utilisateur' => '']));
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $response->andwhere("entity.roles LIKE '%ROLE_ADMIN%'")
                 ->orderBy('entity.id','DESC');
                 
        return $response;
    }


}
