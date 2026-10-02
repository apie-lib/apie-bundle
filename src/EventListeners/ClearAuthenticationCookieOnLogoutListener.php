<?php
namespace Apie\ApieBundle\EventListeners;

use Apie\Common\Events\AddAuthenticationCookie;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Configuring a logout url in symfony does not guarantee that the cookie apie uses for authentication
 * is removed, so you would stay logged in to apie even though symfony logged you out. This listener
 * makes sure the authentication cookie is removed from the response whenever symfony logs a user out.
 */
class ClearAuthenticationCookieOnLogoutListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // run after the logout success handler, so the response is already set.
        return [
            LogoutEvent::class => ['onLogout', -100],
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();
        if ($response === null) {
            return;
        }
        $response->headers->clearCookie(
            AddAuthenticationCookie::COOKIE_NAME,
            '/',
            null,
            true,
            true,
            'lax'
        );
    }
}
