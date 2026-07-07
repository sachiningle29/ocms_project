export function formatIndianNumber(number) {
  if (!number) return '';
  
  // Convert to string and remove all non-digit characters
  const numStr = number.toString().replace(/\D/g, '');
  const num = parseFloat(numStr);
  
  if (isNaN(num)) return '';
  
  // Format with Indian locale (en-IN)
  return new Intl.NumberFormat('en-IN').format(num);
}

export function parseIndianNumber(formattedNumber) {
  if (!formattedNumber) return null;
  // Remove all commas and convert to number
  return parseFloat(formattedNumber.replace(/,/g, ''));
}