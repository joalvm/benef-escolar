const mix = require('laravel-mix');
const webpack = require('webpack');
const TerserPlugin = require("terser-webpack-plugin");
const { resolve } = require('path');

const api_url = process.env.NODE_ENV === 'development' ?
    'http://project-blue.local/api/' :
    'https://blue.sismonitor.com/api/';

const public_css = resolve(__dirname, 'public/static/css');
const public_js = resolve(__dirname, 'public/static/js');

const page_path = (path = '') => {
    return resolve(__dirname, 'resources/views/pages/', path);
};

const template_path = (path = '') => {
    return resolve(__dirname, 'resources/views/templates/', path);
};

const assets_path = (path = '') => {
    return resolve(__dirname, 'resources/assets/', path);
};

mix.browserSync({
    proxy: 'http://project-blue.local/',
    open: true,
});

mix.webpackConfig({
    externals: {
        jquery: 'jQuery',
        'popper.js': 'Popper',
        'lodash': 'lodash',
        'axios': 'axios'
    },
    plugins: [
        new webpack.DefinePlugin({
            'process.env': {
                apiUrl: JSON.stringify(api_url)
            }
        })
    ],
});

mix.options({
    terser: {
        terserOptions: {
            format: {
                comments: false,
            },
        },
        extractComments: false,
    }
});

mix.ts(assets_path("ts/base.ts"), public_js)
    .ts(template_path("initial/initial.ts"), public_js)
    .ts(template_path("admin/admin.ts"), public_js)
    .ts(page_path("register/register.ts"), public_js)
    .ts(page_path("login/login.ts"), public_js)
    .ts(page_path("persons/persons.ts"), public_js)
    .ts(page_path("users/children/user.children.ts"), public_js)
    .ts(page_path("users/bonds/user.bonds.ts"), public_js)
    .ts(page_path("users/bonds/children/user.bonds.children.ts"), public_js)
    .ts(page_path("admin/dashboard/admin.dashboard.ts"), public_js)
    .ts(page_path("admin/persons/admin.persons.ts"), public_js)
    .ts(page_path("admin/education_levels/admin.education.ts"), public_js)
    .ts(page_path("admin/periods/admin.periods.ts"), public_js)
    .ts(page_path("admin/requests/admin.requests.ts"), public_js)
    .ts(page_path("admin/requests/approval/admin.requests.approval.ts"), public_js);

mix.sass(assets_path("scss/base.scss"), public_css)
    .sass(template_path("initial/initial.scss"), public_css)
    .sass(template_path("admin/admin.scss"), public_css)
    .sass(page_path("register/register.scss"), public_css)
    .sass(page_path("login/login.scss"), public_css)
    .sass(page_path("persons/persons.scss"), public_css)
    .sass(page_path("users/children/user.children.scss"), public_css)
    .sass(page_path("users/bonds/user.bonds.scss"), public_css)
    .sass(page_path("users/bonds/children/user.bonds.children.scss"), public_css)
    .sass(page_path("admin/dashboard/admin.dashboard.scss"), public_css)
    .sass(page_path("admin/persons/admin.persons.scss"), public_css)
    .sass(page_path("admin/education_levels/admin.education.scss"), public_css)
    .sass(page_path("admin/periods/admin.periods.scss"), public_css)
    .sass(page_path("admin/requests/admin.requests.scss"), public_css)
    .sass(page_path("admin/requests/approval/admin.requests.approval.scss"), public_css);