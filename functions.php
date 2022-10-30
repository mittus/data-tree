<?php
session_start();

ini_set('display_errors', 'On');
error_reporting(E_ALL);

class Data {
    private $i18n;
    private $pdo;

    public $page;
    public $content;

    public $authorized;
    public $auth;
    public $error;


    function __construct() {
        global $pdo, $db, $i18n;
        $this->i18n =& $i18n;

        $this->pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset='.$db['charset'], $db['login'], $db['password']);
        
        $this->auth();
        $this->getContent();
    }
    /* аутентификация */
    private function auth() {
        if(isset($_SESSION['authorized'])) {
            $this->authorized = true;
            return;
        }

        if(isset($_POST['name']) && isset($_POST['password'])) {

            /* первичная валидация */

            $this->auth['user'] = empty($_POST['name']) ? $this->error('need username') : $_POST['name'];
            $this->auth['password'] = empty($_POST['password']) ? $this->error('need password') : md5($_POST['password']);

            if(empty($this->error)) {

                /* авторизация и вторичная валидация */

                if(isset($_POST['login'])) {
                    $request = $this->pdo->prepare('SELECT * FROM users WHERE name = :name');
                    $request->execute(['name' => $this->auth['user']]);

                    $user = $request->fetch(PDO::FETCH_LAZY);
                    if(empty($user)) {
                        $this->error('empty user');
                    } else if($user->password === $this->auth['password']) {
                        $_SESSION['user'] = $this->auth['user'];
                        $_SESSION['hash'] = $this->auth['password'];
                        $_SESSION['authorized'] = true;
                        $this->authorized = true;
                    } else $this->error('wrong password');
                }
            }
        }
    }
    /* вывод ошибок */
    private function error($srt) {
        $this->error .= '<p>' . $this->i18n($srt) . '</p>';
    }
    public function i18n($str) {
        return isset($this->i18n[$str]) ? $this->i18n[$str] : $str;
    }
    public function session($value) {
        return isset($_SESSION[$value]) ? $_SESSION[$value] : '';
    }
    public function getUrl() {
        if($this->page == 'auth') {
            return '<a href="/">'.$this->i18n('to index').'</a>';
        }
        if($this->authorized) {
            return '<a href="?page=logout">'.$this->i18n('exit').'</a>';
        } else {
            return '<a href="?page=auth">'.$this->i18n('enter').'</a>';
        }
    }
    public function getContent() {
        if(isset($_GET['page'])) {
            $this->page = $_GET['page'] == 'admin' ? 'tree' : $_GET['page'];
            if($this->page == 'auth' && $this->authorized) {
                header('Location: /?page=admin');
            }
            if($this->page == 'logout') {
                session_destroy();
                header('Location: /');
            }
            if($this->page == 'json') {
                $result = [];
                $id = isset($_REQUEST['id']) && $_REQUEST['id'] >= 0 ? $_REQUEST['id'] : null;
                $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : [];

                if($action == 'toggle') {
                    $result = $this->getJsonObjects($_REQUEST['id']);
                }

                if($action == 'show') {
                    $responce = $this->getObject($_REQUEST['id']);
                    $result = array(
                        'html' => '<div>' . ($responce['description'] ? 
                            $responce['description'] : $this->i18n('empty description'))
                        . '</div>'
                    );
                }

                if(!empty($this->authorized)) {
                    if($action == 'add' || $action == 'edit') {
                        $result = $this->getForm($id, $action);
                    }
                    if($action == 'remove') {
                        $result = $this->removeObject($id);
                    }
                }
                
                header('Content-Type: application/json; charset=utf8');
                echo json_encode($result, true);;
                exit;
            }
        } else {
            $this->page = 'tree';
        }
        if(isset($_POST['save'])) {
            $this->sendForm();
        }
        $file = $_SERVER['DOCUMENT_ROOT'].'/view/'.$this->page.'.php';
        if(file_exists($file)) {
            ob_start();
            require_once($file);
            $this->content = ob_get_contents();
            ob_end_clean();
        }
    }
    private function getForm($id, $action) {
        $form = [
            'id' => '',
            'parent' => '',
            'name' => '',
            'description' => '',
        ];
        if($action == 'add') {
            $form['parent'] = $id;
        }
        if($action == 'edit') {
            $object = $this->getObject($id);
            $form = $object;
        }
        ob_start();
        require_once($_SERVER['DOCUMENT_ROOT'].'/view/tree-form.php');
        $form = ob_get_contents();
        ob_end_clean();

        return array(
            'html' => $form,
        );
    }
    private function getTree($id = null) {
        $request = $this->pdo->prepare('SELECT * FROM objects WHERE parent <=> :parent');
        $request->execute(['parent' => $id]);

        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
    private function getObject($id) {
        $request = $this->pdo->prepare('SELECT * FROM objects WHERE id = :id');
        $request->execute(['id' => $id]);

        return $request->fetch(PDO::FETCH_ASSOC);
    }
    private function getJsonObjects($id) {
        $objects = $this->getObjects($id);

        $html = empty($objects) ? [] : '<ul>' . $objects . '</ul>';
        return array(
            'html' => $html,
        );
    }
    public function getAllObjects($parent = false) {
        if(empty($parent)) {
            ob_start();
            $tree = $this->getTree();
        } else $tree = $parent;

        $tree = array_reverse($tree, true);
        
        foreach($tree as $value) {
            $parents = $this->getTree($value['id']);
            require($_SERVER['DOCUMENT_ROOT'].'/view/tree-object.php');
            if(!empty($parents)) {
                echo '<li data-parent="' . $value['id'] . '"><ul>';
                $this->getAllObjects($parents);
                echo '</ul></li>';
            }
        }

        if(empty($parent)) {
            $objects = ob_get_contents();
            ob_end_clean();
            return $objects;
        }
    }
    public function getObjects($id) {
        ob_start();

        $tree = $this->getTree($id);

        $tree = array_reverse($tree, true);
        foreach($tree as $value) {
            $parents = $this->getTree($value['id']);
            require($_SERVER['DOCUMENT_ROOT'].'/view/tree-object.php');
        }
        $objects = ob_get_contents();
        ob_end_clean();
        return $objects;
    }
    private function sendForm() {
        $vars['keys'] = [
            'id' => null,
            'parent' => null,
            'name' => '',
            'description' => '',
        ];
        foreach($vars['keys'] as $key => $var) {
            if($key !== 'id' || $_POST[$key] !== '') {
                $vars['values'][$key] = $_POST[$key] ? $_POST[$key] : $var;
            }
        }

        if(!empty($vars['values']['id'])) {
            $request = $this->pdo->prepare('UPDATE objects SET parent = :parent, name = :name, description = :description WHERE id = :id');
        } else {
            $request = $this->pdo->prepare('INSERT INTO objects SET parent = :parent, name = :name, description = :description');
        }
        $request->execute($vars['values']);
    }
    private function removeObject($id) {

        /* вариант без каскадного удаления на стороне mysql */
        // $sql = "DELETE FROM objects WHERE id IN (SELECT id FROM (SELECT * FROM objects ORDER BY parent, id) AS sort, (SELECT @pv := '$id') AS init WHERE FIND_IN_SET(parent, @pv) > 0 AND @pv := CONCAT(@pv, ',', id)) OR id='$id'";
        // $this->pdo->exec($sql);

        $request = $this->pdo->prepare('DELETE FROM objects WHERE id = :id');
        $request->execute(['id' => $id]);        

        return array(
            'html' => $request,
        );
    }
}




$ob_data = new Data();