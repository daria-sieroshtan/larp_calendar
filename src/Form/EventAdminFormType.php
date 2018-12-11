<?php
namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\EventType;
use App\Entity\Genre;
use App\Entity\Subgenre;
use App\Entity\Region;
use App\Entity\Settlement;
use App\Entity\Weapon;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class EventAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Название'])
            ->add(
                'type',
                EntityType::class,
                [
                    'class' => EventType::class,
                    'choice_label' => 'name',
                    'label' => 'Формат'
                ]
            )
            ->add(
                'startDate',
                DateType::class,
                [
                    'label' => 'Дата начала',
                    'years' => range(date('Y'), date('Y', strtotime('+4 years'))),
                ]
            )
            ->add(
                'endDate',
                DateType::class,
                [
                    'label' => 'Дата окончания',
                    'years' => range(date('Y'), date('Y', strtotime('+4 years'))),
                ]
            )
            ->add(
                'location',
                TextType::class,
                [
                    'label' => 'Место проведения',
                    'required' => false
                ]
            )
            ->add(
                'region',
                EntityType::class,
                [
                    'class' => Region::class,
                    'choice_label' => 'name',
                    'label' => 'Область'
                ]
            )
            ->add(
                'subgenre',
                EntityType::class,
                [
                    'class' => Subgenre::class,
                    'choice_label' => 'name',
                    'required' => true,
                    'label' => 'Жанр, поджанр',
                    'placeholder' => false,
                    'group_by' => function ($choiceValue, $key, $value) {
                        return $choiceValue->getGenre()->getName();
                    }
                ]
            )
            ->add('organizers', TextType::class, ['label' => 'Организаторы'])
            ->add('organizerContact', TextType::class, ['label' => 'Контакты организаторов для обратной связи с Ролендарем'])
            ->add(
                'settlement',
                EntityType::class,
                [
                    'class' => Settlement::class,
                    'choice_label' => 'name',
                    'label' => 'Тип поселения'
                ]
            )
            ->add(
                'weapons',
                EntityType::class,
                [
                    'class' => Weapon::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Тип оружия',
                    'required' => false
                ]
            )
            ->add(
                'priceMin',
                IntegerType::class,
                [
                    'label' => 'Размер денежного взноса от',
                    'required' => false
                ]
            )
            ->add(
                'priceMax',
                IntegerType::class,
                [
                    'label' => 'Размер денежного взноса до',
                    'required' => false
                ]
            )
            ->add(
                'contactSite',
                TextType::class,
                [
                    'label' => 'Сайт мероприятия',
                    'required' => false
                ]
            )
            ->add(
                'contactFB',
                TextType::class,
                [
                    'label' => 'Группа в Facebook',
                    'required' => false
                ]
            )
            ->add(
                'contactVK',
                TextType::class,
                [
                    'label' => 'Группа в Вконтакте',
                    'required' => false
                ]
            )
            ->add(
                'contactTelegram',
                TextType::class,
                [
                    'label' => 'Телеграм',
                    'required' => false
                ]
            )
            ->add(
                'contactOther',
                TextType::class,
                [
                    'label' => 'Другие ссылки и контакты',
                    'required' => false
                ]
            )
            ->add('description', TextareaType::class, ['label' => 'Описание'])
            ->add(
                'picture',
                FileType::class,
                [
                    'label' => 'Загрузить картинку',
                    'help' => 'Размер файла до 2М',
                    'required' => false
                ]
            )

            ->add('mixDeskRuntimeGM', ChoiceType::class, array(
                'choices'  => MixDesk::RuntimeGM['values'],
                'label' => MixDesk::RuntimeGM['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskOpenness', ChoiceType::class, array(
                'choices'  => MixDesk::Openness['values'],
                'label' => MixDesk::Openness['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskPlayerPressure', ChoiceType::class, array(
                'choices'  => MixDesk::PlayerPressure['values'],
                'label' => MixDesk::PlayerPressure['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskCharCreation', ChoiceType::class, array(
                'choices'  => MixDesk::CharCreation['values'],
                'label' => MixDesk::CharCreation['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskMetatechniques', ChoiceType::class, array(
                'choices'  => MixDesk::Metatechniques['values'],
                'label' => MixDesk::Metatechniques['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskStoryEngine', ChoiceType::class, array(
                'choices'  => MixDesk::StoryEngine['values'],
                'label' => MixDesk::StoryEngine['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskCommunicationStyle', ChoiceType::class, array(
                'choices'  => MixDesk::CommunicationStyle['values'],
                'label' => MixDesk::CommunicationStyle['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskBleedIn', ChoiceType::class, array(
                'choices'  => MixDesk::BleedIn['values'],
                'label' => MixDesk::BleedIn['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskLoyaltyToSetting', ChoiceType::class, array(
                'choices'  => MixDesk::LoyaltyToSetting['values'],
                'label' => MixDesk::LoyaltyToSetting['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskRepresentaionOfTheme', ChoiceType::class, array(
                'choices'  => MixDesk::RepresentaionOfTheme['values'],
                'label' => MixDesk::RepresentaionOfTheme['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))
            ->add('mixDeskScenography', ChoiceType::class, array(
                'choices'  => MixDesk::Scenography['values'],
                'label' => MixDesk::Scenography['description'],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ))

            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Сохранить',
                    'attr' => array('class' => 'brand-bg text-light')
                ]
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class,
        ));
    }
}
