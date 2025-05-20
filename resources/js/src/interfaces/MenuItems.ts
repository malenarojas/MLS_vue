export interface MenuItem {
    key: string;
    label: string;
    icon: string | null;
    route: string;
    items?: MenuItem[]; // Submenús
}