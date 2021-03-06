export default [
    {
        path: '/',
        component: require('./pages/welcome').default,
    },
    {
        path: '/:id',
        name: 'cortex',
        component: require('./pages/cortex').default,
    },
    {
        path: '/group/:title',
        name: 'group',
        component: require('./pages/welcome').default,
    },
    {
        path: '/eg/:id',
        name: 'example',
        component: require('./pages/example').default,
    },
    {
        path: '*',
        name: 'catch-all',
        component: require('./pages/404').default,
    },
];
