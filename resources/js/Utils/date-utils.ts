import { parse, format } from "date-fns";
import { addMonths } from "date-fns";
import { parseISO } from "date-fns";

export default class DateUtils {
    public static toDateWithoutTime(dateString: string): Date {
        return parse(dateString, "yyyy-MM-dd ", new Date());
    }

    public static toDate(dateString: string): Date {
        return parseISO(dateString);
    }

    public static toFormattedDate(date: Date): string {
        return format(date, "MM/dd/yyyy");
    }

    public static getCurrentDate() {
        return format(new Date(), "MM/dd/yyyy");
    }

    public static getCurrentDatePlusMonths(months: number): Date {
        return addMonths(this.getCurrentDate(), months);
    }
}
