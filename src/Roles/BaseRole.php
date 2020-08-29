<?php


namespace The7055inc\Shared\Roles;


class BaseRole {

    protected $slug;
    protected $name;
    protected $caps = array();

    public function register() {
        $this->caps = apply_filters('mpl_role_caps', $this->caps);
        add_role($this->slug, $this->name, $this->caps);
    }
}