export const useNumberHelpers = () => {
	const currencyFormat = (value, options = {}) => {
	const formatter = new Intl.NumberFormat('en-US', options)
    return formatter.format(value)
	}
	return { currencyFormat }
  }
