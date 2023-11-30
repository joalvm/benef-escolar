export const STORAGE_PERIOD_KEY = 'selected_period';

function getStoragePeriod(): StoragePeriod | null {
    return (
        JSON.parse(window.localStorage.getItem(STORAGE_PERIOD_KEY)) ?? null
    );
}

window.getStoragePeriod = getStoragePeriod;

export default getStoragePeriod;
