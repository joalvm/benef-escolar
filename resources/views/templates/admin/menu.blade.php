<?php
    use App\Models\Users;

    function active($path)
    {
        $class = 'mdc-list-item--activated';
        $returned = '';

        if (is_string($path) ) {
            $path = [$path];
        }

        foreach ($path as $p) {
            if (request()->route()->uri == $p) {
                $returned = $class;
                break;
            }
        }

        return $returned;
    }

    $menu = [
        [
            'path' => 'admin/dashboard',
            'name' => 'Panel de Control',
            'icon' => 'dashboard',
            'active_class' => active('admin/dashboard'),
            'role' => [Users::ROLE_ADMIN, Users::ROLE_SUPER_ADMIN],
        ],
        [
            'path' => 'admin/persons',
            'name' => 'Trabajadores',
            'icon' => 'people',
            'active_class' => active('admin/persons'),
            'role' => [Users::ROLE_ADMIN, Users::ROLE_SUPER_ADMIN],
        ],
        [
            'path' => 'user/bonds',
            'name' => 'Campaña Escolar',
            'icon' => 'request_quote',
            'active_class' => active('user/bonds'),
            'role' => [Users::ROLE_USER],
        ],
        [
            'path' => 'user/children',
            'name' => 'Hijos',
            'icon' => 'family_restroom',
            'active_class' => active('user/children'),
            'role' => [Users::ROLE_USER],
        ],
        [
            'path' => 'admin/requests',
            'name' => 'Solicitudes',
            'icon' => 'family_restroom',
            'active_class' => active(['admin/requests', 'admin/requests/approval/{id}']),
            'role' => [Users::ROLE_ADMIN, Users::ROLE_SUPER_ADMIN],
        ],
        [
            'path' => 'admin/periods',
            'name' => 'Periodos',
            'icon' => 'date_range',
            'divider' => true,
            'active_class' => active('admin/periods'),
            'role' => [Users::ROLE_ADMIN, Users::ROLE_SUPER_ADMIN],
        ],
        [
            'path' => 'admin/education_levels',
            'name' => 'Niveles Educativos',
            'icon' => 'school',
            'active_class' => active('admin/education_levels'),
            'role' => [Users::ROLE_ADMIN, Users::ROLE_SUPER_ADMIN],
        ],
        [
            'path' => 'person/edit/' . session('user.person_id'),
            'name' => 'Inf. Personal',
            'icon' => 'perm_identity',
            'active_class' => active('person/edit/{id}'),
            'role' => [Users::ROLE_USER, Users::ROLE_ADMIN, Users::ROLE_SUPER_ADMIN],
        ],
        [
            'path' => 'user/formats',
            'name' => 'Formatos',
            'icon' => 'perm_media',
            'active_class' => active('formats'),
            'role' => [Users::ROLE_USER],
        ],
    ];

?>

<aside class="mdc-drawer mdc-drawer--modal mdc-top-app-bar--fixed-adjust">
    <div class="mdc-drawer__header">
        <h3 class="mdc-drawer__title text-overflow">{{ Str::title(session('user.names')) }}</h3>
        <h6 class="mdc-drawer__subtitle"><strong>DNI:</strong>{{ session('user.dni') }}</h6>
    </div>
    <div class="mdc-drawer__content">
        <nav class="mdc-list">
            @foreach ($menu as $item)
            @if (in_array(session('user.role'), $item['role']))
            @if ($item['divider'] ?? false)
            <hr class="mdc-list-divider">
            @endif
            <a class="mdc-list-item {{$item['active_class']}}" href="{{$item['path']}}" aria-current="page">
                <span class="mdc-list-item__ripple"></span>
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">{{$item['icon']}}</i>
                <span class="mdc-list-item__text">{{$item['name']}}</span>
            </a>
            @endif
            @endforeach
            <hr class="mdc-list-divider">
            <a class="mdc-list-item" href="/logout" aria-current="page">
                <span class="mdc-list-item__ripple"></span>
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">logout</i>
                <span class="mdc-list-item__text">Cerrar Sessión</span>
            </a>
        </nav>
    </div>
</aside>
