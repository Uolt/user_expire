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

/**
 * Provides "Expire User" action.
 *
 * @RulesAction(
 *   id = "rules_user_expire",
 *   label = @Translation("Block a user"),
 *   category = @Translation("User"),
 *   context = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user, that should be blocked.")
 *     )
 *   }
 * )
 *
 * @todo: Add access callback information from Drupal 7.
 */
class UserExpire extends RulesActionBase {

}