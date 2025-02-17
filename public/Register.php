<?php
class Register {
    public function __construct() {
        
    }

    public function user_registration_form() {
        ob_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_registration_nonce']) && wp_verify_nonce($_POST['user_registration_nonce'], 'user_registration')) {
            $this->register_user();
        }
        ?>
        <form method="post">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <?php wp_nonce_field('user_registration', 'user_registration_nonce'); ?>
            <input type="submit" value="Register">
        </form>
        <?php
        return ob_get_clean();
    }

    private function register_user() {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $userdata = array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass'  => $password,
        );

        $user_id = wp_insert_user($userdata);

        if (!is_wp_error($user_id)) {
            echo 'User registered successfully.';
        } else {
            echo 'Error: ' . $user_id->get_error_message();
        }
    }
}

new Register();