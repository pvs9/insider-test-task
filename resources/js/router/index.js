import {createWebHistory, createRouter} from "vue-router";
import Home from "../pages/Home";
import NotFound from "../pages/NotFound";
import LeagueFixtures from "../pages/League/LeagueFixtures";
import LeagueProgress from "../pages/League/LeagueProgress";

export const routes = [
    {
        name: 'home',
        path: '/',
        component: Home
    },
    {
        name: 'league',
        path: '/league/:id',
        component: LeagueFixtures,
        props: true
    },
    {
        name: 'league-progress',
        path: '/league/:id/progress',
        component: LeagueProgress,
        props: true
    },
    {
        // path: "*",
        path: "/:catchAll(.*)",
        name: "not-found",
        component: NotFound
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router;
