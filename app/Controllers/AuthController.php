<?php

namespace App\Controllers;
use Hleb\Constructor\Handlers\Request;
use App\Models\UserModel;
use Respect\Factory;
use Lori\Config;
use Lori\Base;

class AuthController extends \MainController
{
    // Показ формы регистрации
    public function registerPage()
    {
        // Если включена инвайт система
        if(Config::get(Config::PARAM_INVITE)) {
            redirect('/invite');
        }
        
        $uid  = Base::getUid();
        $data = [
            'h1'        => lang('Sign up'),
            'canonical' => '/register',
        ];

        // title, description
        Base::Meta(lang('Sign up'), lang('Sign up'), $other = false);
        
        return view(PR_VIEW_DIR . '/auth/register', ['data' => $data, 'uid' => $uid]);    
    }
    
    
    // Показ формы регистрации с инвайтом
    public function registerPageInvite()
    {
        // Код активации
        $code = \Request::get('code');
 
        // Проверяем код
        $invate = UserModel::InvitationAvailable($code);
        if(!$invate) 
        {
            Base::addMsg('Код неверен, или он уже использовался.', 'error');
            redirect('/');   
        }
        
        // http://test.loc/register/invite/61514d8913558958c659b713
        
        $uid  = Base::getUid();
        $data = [
            'h1'        => 'Регистрация по инвайту',
            'canonical' => '/register',
        ];

        // title, description
        Base::Meta('Регистрация по инвайту', 'Регистрация по инвайту', $other = false);
        
        return view(PR_VIEW_DIR . '/auth/register-invate', ['data' => $data, 'uid' => $uid, 'invate' => $invate]);  
    }
    
    // Отправка запроса для регистрации
    public function registerHandler()
    {
        $email      = \Request::getPost('email');
        $login      = \Request::getPost('login');
        $inv_code   = \Request::getPost('invitation_code');
        $inv_uid    = \Request::getPost('invitation_id');
        
        $password = \Request::getPost('password');

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            Base::addMsg(lang('Invalid') . ' email', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
       
        if (!UserModel::replayEmail($email))
        {
            Base::addMsg('Такой e-mail уже есть на сайте', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
       
        // Упростить и в метод
        if (!preg_match('/^[a-zA-Z0-9]+$/u', $login))
        {
            Base::addMsg('В логине можно использовать только латиницу, цифры', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
        
        if (strlen($login) < 4 || strlen($login) > 10)
        {
            Base::addMsg('Логин слишком длинный или короткий', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
 
        if (preg_match('/\s/', $login) || strpos($login,' '))
        {
            Base::addMsg('В логине не допускаются пробелы', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
        
        if (is_numeric(substr($login, 0, 1)))
        {
            Base::addMsg('Логин не может начинаться с цифры', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
        
        for ($i = 0, $l = Base::getStrlen($login); $i < $l; $i++)
        {
            if (self::textCount($login, Base::getStrlen($login, $i, 1)) > 4)
            {
                Base::addMsg('В логине слишком много повторяющихся символов', 'error');
                $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
                redirect($url);
            }
        }

        if (!UserModel::replayLogin($login))
        {
            Base::addMsg('Такой логин уже есть на сайте', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
         
        if (substr_count($password, ' ') > 0)
        {
            Base::addMsg('Пароль не может содержать пробелов', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }

        if (Base::getStrlen($password) < 8 || Base::getStrlen($password) > 24)
        {
            Base::addMsg('Длина пароля должна быть от 8 до 24 знаков', 'error');
            $url = ($inv_code) ? '/register/invite/'.$inv_code : '/register';
            redirect($url);
        }
     
        $reg_ip = Request::getRemoteAddress(); // ip при регистрации 
     
        if(!$inv_code) {
            if (Config::get(Config::PARAM_CAPTCHA)) {
                if (!Base::checkCaptchaCode()) {
                    Base::addMsg('Введеный код не верен', 'error');
                    redirect('/register');
                }
            }
            // Кто пригласил (нам нужны будут данные в таблице users)
            $invitation_id = 0;
        } else {
            $invitation_id = $inv_uid;
        }

        // id удастника
        $active_uid = UserModel::createUser($login, $email, $password, $reg_ip, $invitation_id);

        if($inv_code) {
            // Если регистрация по инвайту, то записываем данные
            UserModel::sendInvitationEmail($inv_code, $inv_uid, $reg_ip, $active_uid);
        } else {
        
            // Активация e-mail
            // Если будет раскомм. то в методе createUser изм. $activated   = 1 на 0
            // $active_uid - id участника
           /* $email_code = Base::randomString('crypto', 20);
            UserModel::sendActivateEmail($active_uid, $email_code);
            
            // Добавим текс письма тут
            $newpass_link = 'https://'. HLEB_MAIN_DOMAIN . '/email/avtivate/' . $code;
            $mail_message = "Activate E-mail: \n" .$newpass_link . "\n\n";
            Base::mailText($email, Config::get(Config::PARAM_NAME).' - email', $mail_message); */
        
        }
        
        Base::addMsg('Регистрация прошла успешно. Введите e-mail и пароль.', 'success');
        redirect('/login');
    }

    // Страница авторизации
    public function loginPage()
    {
        $uid  = Base::getUid();
        $data = [
            'h1'        => lang('Sign in'),
            'canonical' => '/login',
        ];

        // title, description
        Base::Meta(lang('Sign in'), lang('Sign in'), $other = false);

        return view(PR_VIEW_DIR . '/auth/login', ['data' => $data, 'uid' => $uid]);
    }

    // Отправка запроса авторизации
    public function loginHandler()
    {
        $email      = \Request::getPost('email');
        $password   = \Request::getPost('password');
        $rememberMe = \Request::getPost('rememberme');

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Base::addMsg('Недопустимый email', 'error');
            redirect('/login');
        }

        $uInfo = UserModel::getUserInfo($email);

        if (empty($uInfo['id'])) {
            Base::addMsg('Пользователь не существует', 'error');
            redirect('/login');
        }
 
        // Находится ли в бан- листе
        if (UserModel::isBan($uInfo['id'])) {
            Base::addMsg('Ваш аккаунт находится на рассмотрении', 'error');
            redirect('/login');
        }  
        
        // Активирован ли E-mail
        if (!UserModel::isActivated($uInfo['id'])) {
            Base::addMsg('Ваш аккаунт не активирован', 'error');
            redirect('/login');
        }
        
        if (!password_verify($password, $uInfo['password'])) {
            Base::addMsg('E-mail или пароль не верен', 'error');
            redirect('/login');
        } else {
            
            // Если нажал "Запомнить" 
            // Устанавливает сеанс пользователя и регистрирует его
            if ($rememberMe == '1') {
                UserModel::rememberMe($uInfo['id']);
            }
            
            $data = [
                'user_id'       => $uInfo['id'],
                'login'         => $uInfo['login'],
                'email'         => $uInfo['email'],
                'name'          => $uInfo['name'],
                'login'         => $uInfo['login'],
                'avatar'        => $uInfo['avatar'],
                'trust_level'   => $uInfo['trust_level'],
            ];
            
            $last_ip = Request::getRemoteAddress();  
            UserModel::setUserLastLogs($uInfo['id'], $uInfo['login'], $uInfo['trust_level'], $last_ip);
            
            $_SESSION['account'] = $data;
            redirect('/');
        }
    }

    public function logout() 
    { 
       if(!isset($_SESSION)) { session_start(); } 
       session_destroy();
       // Возможно, что нужно очистить все или некоторые cookies
       redirect('/');
    }

    public function recoverPage() 
    {
        $uid  = Base::getUid();
        $data = [
            'h1'        => lang('Password Recovery'),
            'canonical' => '/recover',
        ];

        // title, description
        Base::Meta(lang('Password Recovery'), lang('Password Recovery'), $other = false);
        
        return view(PR_VIEW_DIR . '/auth/recover', ['data' => $data, 'uid' => $uid]);
    }

    public function sendRecover () 
    {
        $email = \Request::getPost('email');
        
        if (Config::get(Config::PARAM_CAPTCHA)) {
            if (!Base::checkCaptchaCode()) {
                Base::addMsg('Введеный код не верен', 'error');
                redirect('/recover');
            }
        }
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           Base::addMsg('Недопустимый email', 'error');
           redirect('/recover');
        }
        
        $uInfo = UserModel::getUserInfo($email);

        if (empty($uInfo['email'])) {
            Base::addMsg('Такого e-mail нет на сайте', 'error');
            redirect('/recover');
        }
        
        // Проверка на заблокированный аккаунт
        if ($uInfo['ban_list'] == 1) {
            Base::addMsg('Вы не можете восстановить пароль', 'error');
            redirect('/recover');
        }
        
        $code = $uInfo['id'] . '-' . Base::randomString('crypto', 25);
        UserModel::initRecover($uInfo['id'], $code);

        // Добавим текс письма тут
        $newpass_link = 'https://'. HLEB_MAIN_DOMAIN . '/recover/remind/' . $code;
        $mail_message = "Your link to change your password: \n" .$newpass_link . "\n\n";

        Base::mailText($email, Config::get(Config::PARAM_NAME).' - changing your password', $mail_message);

        Base::addMsg('Новый пароль отправлен на E-mail', 'success');
        redirect('/login');      
    }
    
    // Страница установки нового пароля
    public function RemindPage()
    {
        // Код активации
        $code = \Request::get('code');
 
        // Проверяем код
        $user_id = UserModel::getPasswordActivate($code);
        if(!$user_id) 
        {
            Base::addMsg('Код неверен, или он уже использовался. Пройдите процедуру восстановления заново.', 'error');
            redirect('/recover');   
        }

        // Получаем пользователя
        if(!$user = UserModel::getUserId($user_id['activate_user_id'])) {
            include HLEB_GLOBAL_DIRECTORY . '/app/Optional/404.php';
            hl_preliminary_exit();
        }
     
        $uid  = Base::getUid();
        $data = [
            'h1'        => 'Восстановление пароля',
            'code'      => $code,
            'user_id'   => $user_id['activate_user_id'],
            'canonical' => 'recover/remind',
        ];

        // title, description
        Base::Meta('Восстановление пароля', 'Восстановление пароля', $other = false);
        
        return view(PR_VIEW_DIR . '/auth/newrecover', ['data' => $data, 'uid' => $uid]);
    }
    
    // Проверка корректности E-mail
    public function AvtivateEmailPage()
    {
        // Код активации
        $code = \Request::get('code');
 
        // Проверяем код
        $avtivate_email = UserModel::getEmailActivate($code);
        if(!$avtivate_email) 
        {
            Base::addMsg('Код неверен, или он уже использовался.', 'error');
            redirect('/');   
        }
        
        UserModel::EmailActivate($avtivate_email['user_id']);
        
        Base::addMsg('Теперь вы можете использовать свой e-mail и пароль.', 'error');
        redirect('/login'); 
    }
    
    
    public function RemindPageNew()
    {
        $password   = \Request::getPost('password');
        $code       = \Request::getPost('code');
        $user_id    = \Request::getPost('user_id');
        
        if(!$user_id) {
            return false;
        }

        if (Base::getStrlen($password) < 8 || Base::getStrlen($password) > 24)
        {
            Base::addMsg('Длина пароля должна быть от 8 до 24 знаков', 'error');
            redirect('/recover/remind/' . $code );
        }
 
        $newpass  = password_hash($password, PASSWORD_BCRYPT);
        $news     = UserModel::editPassword($user_id, $newpass);
         
        if(!$news) {
            return false;
        }
        
        UserModel::editRecoverFlag($user_id);
 
        Base::addMsg('Пароль успешно изменен', 'success');
        redirect('/login');
    }

    // Вхождение подстроки
    private function textCount($str, $needle)
    {
        return mb_substr_count($str, $needle, 'utf-8');
    }

    // Разобьем строки
    private function getSubstr($str, $start, $len)
    {
        return mb_substr($str, $start, $len, 'utf-8');
    }

}
