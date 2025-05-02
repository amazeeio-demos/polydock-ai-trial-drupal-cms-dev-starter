<?php

/**
 * @file
 * Lagoon Drupal 8 configuration file.
 *
 * You should not edit this file, please use environment-specific files!
 * They are loaded in this order:
 * - all.settings.php
 *   For settings that should be applied to all environments.
 * - all.services.yml
 *   For services that should be applied to all environments.
 * - production.settings.php
 *   For settings only for the production environment.
 * - production.services.yml
 *   For services only for the production environment.
 * - development.settings.php
 *   For settings only for all non production environments.
 * - development.services.yml
 *   For services only for all non production environments.
 * - settings.local.php
 *   For settings only for the local environment.
 * - services.local.yml
 *   For services only for the local environment.
 */

// Lagoon version.
if (!defined("LAGOON_VERSION")) {
  define("LAGOON_VERSION", "1");
}

if(getenv('AMAZEE_AI_AI_AUTOMATOR_ADVANCED_MODE_ENABLED')) {
  $settings['ai_automator_advanced_mode_enabled'] = TRUE;
}

// Lagoon database connection.
if (getenv('LAGOON')) {
  $databases['default']['default'] = [
    'driver' => 'mysql',
    'database' => getenv('MARIADB_DATABASE') ?: 'drupal',
    'username' => getenv('MARIADB_USERNAME') ?: 'drupal',
    'password' => getenv('MARIADB_PASSWORD') ?: 'drupal',
    'host' => getenv('MARIADB_HOST') ?: 'mariadb',
    'port' => getenv('MARIADB_PORT') ?: 3306,
    'prefix' => '',
  ];
}

// Lagoon reverse proxy settings.
if (getenv('LAGOON')) {
  $settings['reverse_proxy'] = TRUE;
}

// Trusted Host Patterns.
// Trusted host patterns are not necessary on lagoon as traffic will only
// be routed to your site via the routes (hosts) defined in .lagoon.yml.
if (getenv('LAGOON')) {
  $settings['trusted_host_patterns'][] = '.*';
}

// Temp directory.
if (getenv('TMP')) {
  $config['system.file']['path']['temporary'] = getenv('TMP');
}

// Hash salt.
// Use HASH_SALT if found in the current environment otherwise fallback on
// MARIADB_HOST which is a randomly generated service name.
if (getenv('LAGOON')) {
  $settings['hash_salt'] = hash('sha256', getenv('HASH_SALT') ?: getenv('MARIADB_HOST'));
}

// The default list of directories that will be ignored by Drupal's file API.
if (empty($settings['file_scan_ignore_directories'])) {
  $settings['file_scan_ignore_directories'] = [
    'node_modules',
    'bower_components',
  ];
}

// Settings for all environments.
if (file_exists(__DIR__ . '/all.settings.php')) {
  include __DIR__ . '/all.settings.php';
}

// Services for all environments.
if (file_exists(__DIR__ . '/all.services.yml')) {
  $settings['container_yamls'][] = __DIR__ . '/all.services.yml';
}

if (getenv('LAGOON_ENVIRONMENT_TYPE')) {
  // Environment specific settings files.
  if (file_exists(__DIR__ . '/' . getenv('LAGOON_ENVIRONMENT_TYPE') . '.settings.php')) {
    include __DIR__ . '/' . getenv('LAGOON_ENVIRONMENT_TYPE') . '.settings.php';
  }

  // Environment specific services files.
  if (file_exists(__DIR__ . '/' . getenv('LAGOON_ENVIRONMENT_TYPE') . '.services.yml')) {
    $settings['container_yamls'][] = __DIR__ . '/' . getenv('LAGOON_ENVIRONMENT_TYPE') . '.services.yml';
  }
}


// If we're running inside a Lagoon environment on Kubernetes,
// we want to disable project browser automatic installations
if (getenv('LAGOON_KUBERNETES')) {
  $config['project_browser.admin_settings']['allow_ui_install'] = FALSE;
}


