export let latestConsent = "20230617011200";

export let roles = {
    member: 10,
    mod: 50,
    admin: 90,
    nyx: 100
};

import {faEye, faFileArrowUp, faPenNib, faUsers} from "@fortawesome/free-solid-svg-icons";
export let pages = {
    'index': {
        title: "Start!",
        uri: '/edit',
        icon: faPenNib
    },
    'attributions': {
        title: "Attributions",
        uri: '/edit/attributions',
        icon: faUsers
    },
    'downloads': {
        title: "Downloads & Images",
        uri: '/edit/downloads',
        icon: faFileArrowUp
    },
    'preview': {
        title: 'Preview',
        uri: '/edit/preview',
        icon: faEye
    }
}
