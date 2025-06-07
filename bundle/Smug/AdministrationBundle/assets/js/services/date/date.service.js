class DateService {
  getFormattedDate(date) {
    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    let newDate = new Date(date.date);

    return newDate.toLocaleDateString('de-DE', options);
  }
}
export default new DateService();