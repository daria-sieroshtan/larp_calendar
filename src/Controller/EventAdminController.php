<?php
namespace App\Controller;

use App\Entity\Event;
use App\Form\EventAdminFormType;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\FileUploader;

class EventAdminController extends BaseController
{
    const ITEMS_PER_PAGE_ADMIN = 10;

    /**
     * @Route("/admin", name="admin")
     * @Route("/admin/event/list", name="admin_list_events")
     * @Template("admin/event/list.html.twig")
     */
    public function listEventsAdmin(Request $request, EventRepository $eventRepository)
    {
        $page = $request->get('page', 1);
        $limit = self::ITEMS_PER_PAGE_ADMIN;
        $offset = ($page-1)*$limit;

        $countItems = $eventRepository->countItemsAdmin();

        return
            [
                'entities' => $eventRepository->findListAdmin($limit, $offset),
                'page' => $page,
                'pageCount' => ceil($countItems / $limit),
                'toApprove'=> false
            ];
    }

    /**
     * @Route("/admin/event/list-approve", name="admin_list_events_approve")
     * @Template("admin/event/list.html.twig")
     */
    public function listEventsAdminToApprove(Request $request, EventRepository $eventRepository)
    {
        $page = $request->get('page', 1);
        $limit = self::ITEMS_PER_PAGE_ADMIN;
        $offset = ($page-1)*$limit;

        $countItems = $eventRepository->countItemsAdminToApprove();

        return
            [
                'entities' => $eventRepository->findListAdminToApprove($limit, $offset),
                'page' => $page,
                'pageCount' => ceil($countItems / $limit),
                'toApprove'=> true
            ];
    }

    /**
     * @Route("/admin/event/edit/{id}", name="admin_edit_event", requirements={"id"="\d+"})
     * @Template("/admin/event/edit.html.twig")
     */
    public function editEventAdmin(Request $request, Event $event, FileUploader $fileUploader)
    {
        $initialPicture = $event->getPicture();
        if ($initialPicture) {
            $event->setPicture(
                new File($this->getParameter('pictures_directory') . '/' . $event->getPicture())
            );
        }
        $form = $this->createForm(EventAdminFormType::class, $event);
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

            $this->addSuccessFlash(sprintf('Событие "%s" отредактировано', $event->getName()));
            return $this->redirectToRoute('admin_list_events');
        }

        return ['form' => $form->createView()];
    }


    /**
     * @Route("/admin/event/delete/{id}", name="delete_event", requirements={"id"="\d+"})
     */
    public function deleteEvent(Event $event)
    {
        $this->removeEntity($event);
        $this->addSuccessFlash('Successfully deleted event');

        return $this->redirectToRoute('admin_list_events');
    }

    /**
     * @Route("/admin/event/cancel/{id}", name="cancel_event", requirements={"id"="\d+"})
     */
    public function changeStatusToCancelled(Event $event)
    {
        $event->setStatus(Event::STATUS_CANCELLED);
        $this->saveEntity($event);
        $this->addSuccessFlash('Событие отмечено как отмененное');

        return $this->redirectToRoute('admin_list_events');
    }

    /**
     * @Route("/admin/event/approve/{id}", name="approve_event", requirements={"id"="\d+"})
     */
    public function changeStatusToApproved(Event $event)
    {
        $event->setStatus(Event::STATUS_APPROVED);
        $event->setPublishDate(new \DateTime(date('Y-m-d')));
        $this->saveEntity($event);
        $this->addSuccessFlash('Событие подтверждено и опубликовано');

        return $this->redirectToRoute('admin_list_events_approve');
    }

    /**
     * @Route("/admin/event/restore/{id}", name="restore_event", requirements={"id"="\d+"})
     */
    public function restoreCancelledEvent(Event $event)
    {
        $event->setStatus(Event::STATUS_APPROVED);
        $this->saveEntity($event);
        $this->addSuccessFlash('Событие отмечено как актуальное');

        return $this->redirectToRoute('admin_list_events');
    }
}
