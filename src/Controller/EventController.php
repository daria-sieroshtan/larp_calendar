<?php
namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Form\EventSearchFormType;
use App\Repository\EventRepository;
use App\Service\AdminNotificationMailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;

class EventController extends BaseController
{
    const ITEMS_PER_PAGE = 8;
    const FIRST_YEAR = 2013;

    /**
     * @Route("/event/calendar", name="calendar")
     * @Template("calendar.html.twig")
     *
     */
    public function viewCalendar()
    {
        return [];
    }


    /**
     * @Route("/event/list/{page}/",
     *      name="list_events",
     *     defaults={"page"=1},
     *     requirements={"page"="%routing.page%"}
     *     )
     * @Template("event/event_list.html.twig")
     */
    public function listEvent(Request $request, EventRepository $eventRepository, PaginatorInterface $paginator, $page)
    {
        $pagination = $paginator->paginate($eventRepository->findListForPagination(), $page, self::ITEMS_PER_PAGE);

        return
            [
                'pagination' => $pagination,
            ];
    }

    /**
     * @Route("/event/search", name="search_events")
     * @Template("event/event_search.html.twig")
     */
    public function searchEvent(Request $request, EventRepository $eventRepository)
    {
        $form = $this->createForm(EventSearchFormType::class);
        $form->handleRequest($request);
        $years = range(self::FIRST_YEAR, date('Y', strtotime('+1 year')));

        if ($form->isSubmitted() && $form->isValid()) {
            $params = $form->getData();

            return [
                'events' => $eventRepository->findListByCriterias($params),
                'form' => $form->createView(),
                'years' => $years,
                'showResult' => true,
            ];
        }

        return
            [
                'events' => [],
                'form' => $form->createView(),
                'years' => $years,
                'showResult' => false,
            ];
    }

    /**
     * @Route("event/add", name="new_event")
     * @Template("event/event_add.html.twig")
     */
    public function addEvent(Request $request, FileUploader $fileUploader, AdminNotificationMailer $notificationMailer)
    {
        $event = new Event;
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Event $event */
            $event = $form->getData();
            $file = $form->get('picture')->getData();
            $fileName = $fileUploader->upload($file);
            $event->setPicture($fileName);

            $token = $this->generateToken(20);
            $event->setToken($token);
            $event->setStatus(Event::STATUS_PENDING);

            $this->saveEntity($event);

            $notificationMailer->sendEventCreatedNotification($event);

            $url = $this->generateUrl(
                'edit_event',
                [
                    'id' => $event->getId(),
                    'token' => $event->getToken()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $this->addSuccessFlash(sprintf("Событие создано и будет опубликовано после утверждения администратором.<br>
            Вы можете редактировать событие по ссылке: %s<br>
            Пожалуйста, будьте осторожны: распространение этой ссылки позволит другим также редактировать вашу информацию.", $url));
            return $this->redirectToRoute('home');
        }

        return ['form' => $form->createView()] ;
    }

    /**
     * @Route("/event/{id}", name="show_event", requirements={"id"="\d+"})
     * @Route("/event/{slug}", name="show_event_by_slug")
     * @Template("event/event_show.html.twig")
     */
    public function showEvent(Event $event)
    {
        return ['event'=>$event];
    }

    /**
     * @Route("/event/edit/{id}/{token}", name="edit_event", requirements={"id"="\d+"})
     * @Template("/event/event_edit.html.twig")
     */
    public function editEvent(Request $request, Event $event, $token, FileUploader $fileUploader, AdminNotificationMailer $notificationMailer)
    {
        if ($event->getToken() != $token) {
            throw $this->createNotFoundException('Возможно, вы пытаетесь отредактировать ранее созданное событие, но это неправильная ссылка. 
            В таком случае обратитесь к администратору.');
        }

        $initialEvent = clone $event;
        $initialPicture = $event->getPicture();
        if ($initialPicture) {
            $event->setPicture(
                new File($this->getParameter('pictures_directory') . '/' . $event->getPicture())
            );
        }
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $file = $form->get('picture')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if ($fileName) {
                    $event->setPicture($fileName);
                }
            } else {
                $event->setPicture($initialPicture);
            }

            $this->saveEntity($event);

            $notificationMailer->sendEventEditedNotification($initialEvent, $event);

            $this->addSuccessFlash(sprintf('Событие "%s" отредактировано.', $event->getName()));
            $this->addNoticeFlash('Вы редактируете ранее созданное событие. <br>
            Пожалуйста, будьте осторожны: распространение этой ссылки позволит другим также редактировать вашу информацию.');
            return ['form' => $form->createView()];
        }

        $this->addNoticeFlash('Вы редактируете ранее созданное событие.<br>
        Пожалуйста, будьте осторожны: распространение этой ссылки позволит другим также редактировать вашу информацию.');
        return ['form' => $form->createView()];
    }
}
