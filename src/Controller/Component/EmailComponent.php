<?php 

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

class EmailComponent extends Component {

    public function send($data, $template, $layout, $subject, $send_to) {
       
        $mailer = new Mailer();

        // using gmail transport
        $mailer->setTransport('gmail'); // read from app.php
        
        $mailer->setFrom(['rockman1688@gmail.com' => 'Rockman'])
                ->setTo($send_to)
                ->setSubject($subject)
                ->setEmailFormat('html')
                ->viewBuilder()
                    ->setTemplate($template)    // templage/email/html/welcome.php (less empty), same as frontend
                    ->setLayout($layout);       // template/layout/email/html/welcome

        $mailer->setViewVars(
            [
                'data' => $data,
            ],
        );

        return  $mailer->send();
    }

    public function send_v1($data) {
       
        $mailer = new Mailer();

        // using gmail transport
        $mailer->setTransport('gmail'); // read from app.php
        
        $mailer->setFrom(['rockman1688@gmail.com' => 'Rockman'])
        // $mailer->setFrom(['rwowltd@15478.club' => 'wow'])
                ->setTo('vi.lh@vtl-vtl.com')
                ->setCC('kienminh.chans@gmail.com')
                ->setSubject('Test Email Cakephp 4.2.5 - Mailer')
                ->setEmailFormat('html')
                ->viewBuilder()
                    ->setTemplate('welcome')    // templage/email/html/welcome.php (less empty), same as frontend
                    ->setLayout('welcome');     // template/layout/email/html/welcome

        $mailer->setViewVars(
            [
                'value1' => $data['value1'],
                'value2' => $data['value2'],
            ],
        );

        return  $mailer->send();
    }

    public function call_email_func($param) {
        return 'abc';
    }
}