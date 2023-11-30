import { MDCTooltip } from "@material/tooltip";
import { MDCDialog } from "@material/dialog";
import { MDCList } from "@material/list";
import { STORAGE_PERIOD_KEY } from "../../../assets/ts/helpers/getStoragePeriod";

interface Period {
    id: number;
    name: string;
    active: boolean;
    start_date: Date;
    finish_date: Date;
    amount_bonds: number;
    max_amount_loan: number;
    max_children: number;
    created_at: Date;
}

class Periods {
    private storage: StoragePeriod;
    private btnPeriod: HTMLButtonElement;
    private dialog: MDCDialog;
    private list: MDCList;

    constructor() {
        this.storage = getStoragePeriod();

        this.init();
        this.initEvents();

        if (!this.storage) {
            this.dialog.open();
        } else {
            this.btnPeriod.querySelector(
                ".mdc-button__label"
            ).textContent = this.storage.name;
        }
    }

    private init() {
        this.btnPeriod = document.getElementById(
            "btn-period"
        ) as HTMLButtonElement;

        new MDCTooltip(document.getElementById("btn-period-tooltip"));

        this.dialog = new MDCDialog(
            document.querySelector("#dialog-selected_period")
        );

        this.dialog.scrimClickAction = "";
        this.dialog.escapeKeyAction = "";

        this.list = new MDCList(this.dialog.root.querySelector(".mdc-list"));
    }

    private initEvents() {
        this.btnPeriod.addEventListener(
            "click",
            this.onClickOpenDialog.bind(this)
        );
        this.dialog.listen("MDCDialog:opened", this.onOpenDialog.bind(this));
    }

    private onClickOpenDialog() {
        this.dialog.open();
    }

    private async onOpenDialog() {
        this.list.initialize();

        await this.getPeriods();

        this.list.layout();
    }

    private async getPeriods() {
        const periods = await Http.api().get<Period[]>("periods", {
            paginate: false,
        });

        periods.data.forEach((period, index) => {
            this.list.root.append(this.itemList(period, index));
        });

        this.list.initialSyncWithDOM();
    }

    private itemList(period: Period, index: number): HTMLLIElement {
        const li = createElement("li", { className: ["mdc-list-item"] });

        li.setAttribute("tabindex", index.toString());

        li.append(
            ...[this.radioItemList(period), this.labelItemList(period, index)]
        );

        return li;
    }

    private radioItemList(period: Period): HTMLSpanElement {
        let span = createElement("span", {
            className: ["mdc-list-item__graphic"],
        });
        let radio = createElement("div", { className: ["mdc-radio"] });
        let background = createElement("div", {
            className: ["mdc-radio__background"],
        });
        let input = createElement("input", {
            className: ["mdc-radio__native-control"],
            id: `period-${period.id}`,
            name: "periods",
            dataset: { name: period.name },
        });

        input.setAttribute("type", "radio");
        input.setAttribute("value", period.id.toString());
        input.setAttribute("required", "true");

        let active = this.storage
            ? this.storage.id == period.id
            : period.active;

        if (active) {
            input.setAttribute("checked", "true");
        }

        background.append(
            ...[
                createElement("div", {
                    className: ["mdc-radio__outer-circle"],
                }),
                createElement("div", {
                    className: ["mdc-radio__inner-circle"],
                }),
            ]
        );

        radio.append(...[input, background]);
        span.append(radio);

        return span;
    }

    private labelItemList(period: Period, index: number): HTMLLabelElement {
        const label = createElement("label", {
            id: `period-label-${index}`,
            className: ["mdc-list-item__text"],
        });

        label.setAttribute("for", `period-${period.id}`);
        label.textContent = period.name;

        if (period.active) {
            let actual = createElement("small");
            actual.textContent = "(Actual)";
            label.append(actual);
        }

        return label;
    }
}

export default Periods;
