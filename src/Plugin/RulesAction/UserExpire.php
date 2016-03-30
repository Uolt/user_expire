<?php
/**
 * @file
 * Contains \Drupal\user_expire\Plugin\RulesAction\UserExpire.
 */

namespace Drupal\user_expire\Plugin\RulesAction;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;


/**
 * Provides "Expire User" action.
 *
 * @RulesAction(
 *   id = "rules_user_expire",
 *   label = @Translation("Set a user expiration date"),
 *   category = @Translation("User"),
 *   context = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user, that should be expired.")
 *     )
 *    "expiration_date" = @ContextDefinition("date",
 *       label = @Translation("Date"),
 *       description = @Translation("Specifies the date, when user should be expired.")
 *     )
 *   }
 * )
 *
 */
class UserExpire extends RulesActionBase {

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a \Drupal\user_expire\Plugin\RulesAction\UserExpire object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *    The database service.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Expire user.
   *
   * @param \Drupal\user\UserInterface $user
   *    The user object.
   */
  protected function doExecute(UserInterface $user, $expiration = NULL) {
    if (!empty($expiration)) {
      // If there's an expiration, save it.
      $this->database->merge('user_expire')
        ->key(array('uid' => $user->id()))
        ->fields(array(
          'uid' => $user->id(),
          'expiration' => $expiration,
        ))
        ->execute();

      $user->expiration = $expiration;
      user_expire_notify_user($user);
    }
    else {
      // If the expiration is not set, delete any value that might be set.
      if (!$user->isNew()) {
        // New accounts can't have a record to delete.
        // Existing records (!is_new) might.

        // Remove user expiration times for this user.
        $deleted = $this->database->delete('user_expire')
          ->condition('uid', $user->id())
          ->execute();

        // Notify user that expiration time has been deleted.
        if ($deleted) {
          drupal_set_message($this->t("%name's expiration date has been reset.", array('%name' => $user->getAccountName())));
        }
      }
    }
  }
}