<?php namespace Flarum\Core\Users\Listeners;

use Flarum\Core\Users\User;
use Flarum\Core\Posts\Events\PostWasPosted;
use Flarum\Core\Posts\Events\PostWasDeleted;
use Flarum\Core\Posts\Events\PostWasHidden;
use Flarum\Core\Posts\Events\PostWasRestored;
use Flarum\Core\Discussions\Events\DiscussionWasStarted;
use Flarum\Core\Discussions\Events\DiscussionWasDeleted;
use Illuminate\Contracts\Events\Dispatcher;

class UserMetadataUpdater
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(PostWasPosted::class, __CLASS__.'@whenPostWasPosted');
        $events->listen(PostWasDeleted::class, __CLASS__.'@whenPostWasDeleted');
        $events->listen(PostWasHidden::class, __CLASS__.'@whenPostWasHidden');
        $events->listen(PostWasRestored::class, __CLASS__.'@whenPostWasRestored');
        $events->listen(DiscussionWasStarted::class, __CLASS__.'@whenDiscussionWasStarted');
        $events->listen(DiscussionWasDeleted::class, __CLASS__.'@whenDiscussionWasDeleted');
    }

    /**
     * @param PostWasPosted $event
     */
    public function whenPostWasPosted(PostWasPosted $event)
    {
        $this->updateCommentsCount($event->post->user, 1);
    }

    /**
     * @param PostWasDeleted $event
     */
    public function whenPostWasDeleted(PostWasDeleted $event)
    {
        $this->updateCommentsCount($event->post->user, -1);
    }

    /**
     * @param PostWasHidden $event
     */
    public function whenPostWasHidden(PostWasHidden $event)
    {
        $this->updateCommentsCount($event->post->user, -1);
    }

    /**
     * @param PostWasRestored $event
     */
    public function whenPostWasRestored(PostWasRestored $event)
    {
        $this->updateCommentsCount($event->post->user, 1);
    }

    /**
     * @param DiscussionWasStarted $event
     */
    public function whenDiscussionWasStarted(DiscussionWasStarted $event)
    {
        $this->updateDiscussionsCount($event->discussion->startUser, 1);
    }

    /**
     * @param DiscussionWasDeleted $event
     */
    public function whenDiscussionWasDeleted(DiscussionWasDeleted $event)
    {
        $this->updateDiscussionsCount($event->discussion->startUser, -1);
    }

    /**
     * @param User $user
     * @param int $amount
     */
    protected function updateCommentsCount(User $user, $amount)
    {
        $user->comments_count += $amount;
        $user->save();
    }

    /**
     * @param User $user
     * @param int $amount
     */
    protected function updateDiscussionsCount(User $user, $amount)
    {
        $user->discussions_count += $amount;
        $user->save();
    }
}
