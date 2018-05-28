<?php

namespace Vicoders\Tool;

use App\Models\Petition;
use App\Models\Signal;
use Illuminate\Support\ServiceProvider;
use NF\Facades\App;
use NF\Facades\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vicoders\Tool\Export;
use Vicoders\Tool\Facades\Export as FacadeExport;

class ToolServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('ToolView', function ($app) {
            $view = new \NF\View\View;
            $view->setViewPath(__DIR__ . '/../resources/views');
            $view->setCachePath(__DIR__ . '/../resources/cache');
            return $view;
        });
        $this->app->singleton('Export', function ($app) {
            return new Export;
        });

        $this->registerAdminPostAction();

        if (is_admin()) {
            $this->registerOptionPage();
        }
    }

    public function registerCommand()
    {
        // Register your command here, they will be bootstrapped at console
        //
        // return [
        //     PublishCommand::class,
        // ];
    }

    public function registerAdminPostAction()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_media();
        });

        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style(
                'vicoders-tools-style',
                wp_slash(get_stylesheet_directory_uri() . '/tools/assets/dist/app.css'),
                false
            );
            wp_enqueue_script(
                'vicoders-tools-scripts',
                wp_slash(get_stylesheet_directory_uri() . '/tools/assets/dist/app.js'),
                'jquery',
                '1.0',
                true
            );
        });

        add_action('wp_ajax_export_file', [$this, 'handleExportFile']);
        add_action('wp_ajax_nopriv_export_file', [$this, 'handleExportFile']);
    }

    public function registerOptionPage()
    {
        add_action('media_buttons', [$this, 'renderExport'], 15);
    }

    public function renderExport($post_id)
    {
        echo '<button type="button" id="export-button" data-id="' . $GLOBALS['post_ID'] . '" class="button">Export</button>';
    }

    public function handleExportFile()
    {
        if (!Request::has('post_id')) {
            return false;
        }
        $post_id = Request::get('post_id');

        $post      = get_post($post_id);
        $name_file = $post->post_title;
        $petition  = Petition::where('post_id', $post->ID)->first();

        if (empty($petition->toArray())) {
            throw new BadRequestHttpException("Petition empty", null, 1);
        }

        $signals = Signal::where('petition_id', $post->ID)->get();
        if (empty($signals->toArray())) {
            throw new BadRequestHttpException("Signal empty", null, 1);
        }

        $exports   = [];
        $exports[] = [
            'TÊN KIẾN NGHỊ'       => $post->post_title,
            'SỐ CHỮ KÝ MONG MUỐN' => $petition->number_signature,
            'NGƯỜI VIẾT BÀI'      => title_case(get_the_author_meta('last_name', $post->post_author)),
            'NGÀY TẠO'            => $petition->created_at,
        ];
        $exports[] = [];
        $exports[] = [];

        $exports_second = [];
        foreach ($signals as $key => $item) {
            $user             = get_user_meta($item->signer_id);
            $user_email            = get_userdata($item->signer_id);

            $exports_second[] = [
                'TÊN NGƯỜI KÝ'               => $user_email->data->display_name,
                'EMAIL'             => $user_email->data->user_email,
                'ĐỊA CHỈ'           => $user['address'][0],
                'THÀNH PHỐ'         => $user['city'][0],
                'SỐ ĐIỆN THOẠI'     => $user['phone'][0],
                'NGÀY KÝ KIẾN NGHỊ' => $item->created_at,
            ];
        }

        $path = FacadeExport::exportFile($name_file, $exports, $exports_second, 'csv');
        wp_send_json(['status' => 1, 'data' => $path]);
    }
}
