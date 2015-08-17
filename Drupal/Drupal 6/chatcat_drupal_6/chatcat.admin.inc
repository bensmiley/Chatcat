<?php

/**
 * @file
 * Ubercart currency admin configuration page
 */

/**
 * Tmplements hook_form().
 */
function chatcat_settings_form() {
  $form = array();

  $form['chatcat-api-key'] = array(
    '#type' => 'textfield',
    '#title' => 'Chatcat API Key',
    '#description' => 'If left blank, single sign on will be disabled.',
    '#value' =>  variable_get('chatcat-api-key',''),
  );

  $form['chatcat-secret'] = array(
    '#type' => 'textfield',
    '#title' => 'Chatcat Secret',
    '#description' => 'You can find your secret at dev.chatcatapp.com',
    '#value' =>  variable_get('chatcat-secret',''),
  );

 $form['chatcat-login-url'] = array(
    '#type' => 'textfield',
    '#title' => 'Login URL',
    '#description' => 'Enter custom login URL',
    '#value' => variable_get('chatcat-login-url',''),
  );

  $form['chatcat-register-url'] = array(
    '#type' => 'textfield',
    '#title' => 'Registration URL',
    '#description' => 'Enter custom registration URL',
    '#value' => variable_get('chatcat-register-url',''),
  );

 $form['chatcat-primary-domain'] = array(
    '#type' => 'textfield',
    '#title' => 'Primary URL ',
    '#description' => '',
    '#value' => variable_get('chatcat_primary-domain',''),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Save',
  );

  return $form;
}

function chatcat_settings_form_validate($form, &$form_state) {
    //$num = strlen($_POST['chatcat_api']);
    //if (strlen($_POST['chatcat_api']) != 0 && strlen($_POST['chatcat_api']) != 40) {
	//print $num;
	//die;
    //    form_set_error('chatcat_api', t('You have added an invalid API Key.'));
    //}
}

/**
 * Implements chatcat_settings_form().
 */
function chatcat_settings_form_submit($form, &$form_state) {

    variable_set('chatcat-api-key', $_POST['chatcat-api-key']);
	variable_set('chatcat-login-url', $_POST['chatcat-login-url']);
	variable_set('chatcat-register-url', $_POST['chatcat-register-url']);
    variable_set('chatcat-primary-domain', $_POST['chatcat-primary-domain']);
    variable_set('chatcat-secret', $_POST['chatcat-secret']);

    drupal_set_message(t('Chatcat configurations saved'), 'status');
}
