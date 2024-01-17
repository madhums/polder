<?php

namespace FluentFormPro\classes\Chat;

use FluentForm\Framework\Helpers\ArrayHelper;

/**
 *  Handling Chat Field Module.
 *
 * @since 4.3.13
 */
class ChatFieldController
{
    protected $optionKey = '_fluentform_openai_settings';
    protected $integrationKey = 'openai';

    public function boot()
    {
        $isEnabled = $this->isEnabled();

        add_filter('fluentform/global_addons', function($addOns) use ($isEnabled) {
            $addOns[$this->integrationKey] = [
                'title'       => 'OpenAI ChatGPT Integration',
                'description' => __('Connect OpenAI ChatGPT Integration with Fluent Forms', 'fluentformpro'),
                'logo'        => fluentFormMix('img/integrations/openai.png'),
                'enabled'     => ($isEnabled) ? 'yes' : 'no',
                'config_url'  => admin_url('admin.php?page=fluent_forms_settings#general-openai-settings'),
                'category'    => '', //Category : All
            ];

            return $addOns;
        }, 9);

        if (!$isEnabled) {
            return;
        }

        add_filter('fluentform/global_settings_components', [$this, 'addGlobalMenu'], 10, 1);

        add_filter('fluentform/global_integration_settings_' . $this->integrationKey, [$this, 'getGlobalSettings'], 11,
            1);

        add_filter('fluentform/global_integration_fields_' . $this->integrationKey, [$this, 'getGlobalFields'], 11, 1);

        add_action('fluentform/save_global_integration_settings_' . $this->integrationKey,
            [$this, 'saveGlobalSettings'], 11, 1);

        new ChatField();
    }

    public function getGlobalSettings($settings)
    {
        $globalSettings = get_option($this->optionKey);
        if (!$globalSettings) {
            $globalSettings = [];
        }
        $defaults = [
            'access_token' => ''
        ];

        return wp_parse_args($globalSettings, $defaults);
    }

    public function getGlobalFields($fields)
    {
        return [
            'logo'             => fluentFormMix('img/integrations/openai.png'),
            'menu_title'       => __('OpenAI ChatGPT Integration', 'fluentformpro'),
            'menu_description' => __('The OpenAI API can be applied to chat directly with Chat GPT within fluent forms.',
                'fluentformpro'),
            'valid_message'    => __('Your OpenAI connection is valid', 'fluentformpro'),
            'invalid_message'  => __('Your OpenAI connection is not valid', 'fluentformpro'),
            'save_button_text' => __('Verify OpenAI', 'fluentformpro'),
            'fields'           => [
                'button_link'  => [
                    'type'      => 'link',
                    'link_text' => __('Get OpenAI API Keys', 'fluentformpro'),
                    'link'      => 'https://platform.openai.com/account/api-keys',
                    'target'    => '_blank',
                    'tips'      => __('Please click on this link get API keys from OpenAI.', 'fluentformpro'),
                ],
                'access_token' => [
                    'type'        => 'password',
                    'placeholder' => __('API Keys', 'fluentformpro'),
                    'label_tips'  => __("Please find API Keys by clicking 'Get OpenAI API Keys' Button then paste it here",
                        'fluentformpro'),
                    'label'       => __('Access Code', 'fluentformpro'),
                ]
            ],
            'hide_on_valid'    => true,
            'discard_settings' => [
                'section_description' => __('Your OpenAI integration is up and running', 'fluentformpro'),
                'button_text'         => __('Disconnect OpenAI', 'fluentformpro'),
                'data'                => [
                    'access_token' => ''
                ],
                'show_verify'         => true
            ]
        ];
    }

    public function saveGlobalSettings($settings)
    {
        $token = $settings['access_token'];
        if (empty($token)) {
            $integrationSettings = [
                'access_token' => '',
                'status'       => false
            ];
            // Update the reCaptcha details with siteKey & secretKey.
            update_option($this->optionKey, $integrationSettings, 'no');
            wp_send_json_success([
                'message' => __('Your settings has been updated', 'fluentformpro'),
                'status'  => false
            ], 200);
        }

        // Verify API key now
        try {
            $isAuth = $this->isAuthenticated($token);

            if ($isAuth && !is_wp_error($isAuth)) {
                $token = [
                    'status'       => true,
                    'access_token' => $settings['access_token']
                ];
            } else {
                throw new \Exception($isAuth->get_error_message(), $isAuth->get_error_code());
            }

            update_option($this->optionKey, $token, 'no');
        } catch (\Exception $exception) {
            wp_send_json_error([
                'message' => $exception->getMessage()
            ], 400);
        }

        wp_send_json_success([
            'message' => __('Your OpenAI API key has been verified and successfully set', 'fluentformpro'),
            'status'  => true
        ], 200);
    }

    public function addGlobalMenu($setting)
    {
        $setting[$this->integrationKey] = [
            'hash'         => 'general-' . $this->integrationKey . '-settings',
            'component'    => 'general-integration-settings',
            'settings_key' => $this->integrationKey,
            'title'        => 'OpenAI ChatGPT Integration',
        ];
        return $setting;
    }

    public function pushIntegration($integrations, $formId)
    {
        $integrations[$this->integrationKey] = [
            'title'                 => 'OpenAI Chat GPT Integration',
            'logo'                  => fluentFormMix('img/integrations/openai.png'),
            'is_active'             => $this->isEnabled(),
            'configure_title'       => __('Configuration required!', 'fluentformpro'),
            'global_configure_url'  => admin_url('admin.php?page=fluent_forms_settings#general-openai-settings'),
            'configure_message'     => __('OpenAI is not configured yet! Please configure your OpenAI api first',
                'fluentformpro'),
            'configure_button_text' => __('Set OpenAI API', 'fluentformpro')
        ];
        return $integrations;
    }

    public function isEnabled()
    {
        $globalModules = get_option('fluentform_global_modules_status');
        $inventoryModule = ArrayHelper::get($globalModules, $this->integrationKey);
        if ($inventoryModule == 'yes') {
            return true;
        }
        return false;
    }

    public function makeRequest($token)
    {
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
        ];

        $bodyArgs = [
            "model"    => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role"    => "system",
                    "content" => "You are a helpful assistant."
                ]
            ]
        ];

        $request = wp_remote_post($url, [
            'headers' => $headers,
            'body'    => json_encode($bodyArgs)
        ]);

        if (is_wp_error($request)) {
            $message = $request->get_error_message();
            return new \WP_Error(423, $message);
        }

        $body = json_decode(wp_remote_retrieve_body($request), true);
        $code = wp_remote_retrieve_response_code($request);

        if ($code !== 200) {
            $error = __('Something went wrong.', 'fluentformpro');
            if (isset($body['error']['message'])) {
                $error = __($body['error']['message'], 'fluentformpro');
            }
            return new \WP_Error(423, $error);
        }

        return $body;
    }

    public function isAuthenticated($token)
    {
        $result = $this->makeRequest($token);
        if (is_wp_error($result)) {
            return $result;
        }
        return ArrayHelper::exists($result, 'id');
    }
}
