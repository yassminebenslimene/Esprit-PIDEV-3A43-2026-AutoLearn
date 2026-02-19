<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Evenement;
use App\Entity\Participation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        
        $builder
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => function(Evenement $evenement) {
                    return $evenement->getTitre() . ' - ' . $evenement->getLieu() . ' (' . $evenement->getDateDebut()->format('d/m/Y') . ')';
                },
                'label' => 'Événement',
                'placeholder' => 'Sélectionnez un événement',
                'help' => 'Choisissez l\'événement auquel vous souhaitez participer'
            ])
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'label' => 'Votre équipe',
                'placeholder' => 'Sélectionnez votre équipe',
                'query_builder' => function($repository) use ($user) {
                    // Afficher seulement les équipes dont l'utilisateur est membre
                    return $repository->createQueryBuilder('e')
                        ->join('e.etudiants', 'et')
                        ->where('et.id = :userId')
                        ->setParameter('userId', $user->getId());
                },
                'help' => 'Sélectionnez l\'équipe avec laquelle vous souhaitez participer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
            'user' => null,
        ]);
        
        $resolver->setRequired('user');
    }
}
