import RandExp from "randexp";

class TextService {
  getOutput(value, length = 56) {
    if (typeof value !== 'string') {
      if (Array.isArray(value)) {
        return this.truncate(value[0], length, '...'); 
      }

      return value;
    }

    return this.truncate(value, length, '...');
  }
  truncate(text, length, suffix) {
    if (typeof text === 'undefined') {
      return '';
    }
    if (text.length > length) {
      if (text.includes('<p>')) {
        return text.substring(0, length) + suffix + '</p>';
      } else {
        return text.substring(0, length) + suffix;
      }
    } else {
      return text;
    }
  }
  transformRegexToReadableExample(regex) {
    const randexp = new RandExp(regex);
    const example = randexp.gen();

    return example;
  }
}
export default new TextService();