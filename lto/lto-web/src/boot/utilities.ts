
import moment from 'moment';

interface Result {
  error: string;
  status: boolean;
}


export const getAge = (birthDate: string): number => {
  return moment().diff(moment(birthDate, 'YYYYMMDD'), 'years');
};

export const addComma = (val: object) => {
  if (val) {
    return val + ',';
  }
};

export const getPriceRange = (priceArray: Array<object>) => {
  if (!priceArray) {
    return '';
  }
  // Extract online prices from the array
  const prices = priceArray.map((item) => item.online_price);

  // Find the minimum and maximum prices
  const minPrice = Math.min(...prices);
  const maxPrice = Math.max(...prices);

  if (prices.length > 1) {
    return `₱ ${minPrice.toFixed(2)} - ₱${maxPrice.toFixed(2)}`;
  }
  return `₱ ${minPrice.toFixed(2)}`;
};

export const capitalizeWord = (string) => {
  if (string) {
    return string.replace(/(^\w|\s\w)/g, (m) => m.toUpperCase());
  }
  return '';
};

export function decimalThousandSeparator(value: number) {
  if (value) {
    return (
      currency() + ' ' + value?.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&, ')
    );
  }
  return '₱ 0.00';
}

export const currency = (): string =>  {
  return '₱';
}

export const formatMoney = (money: number) :string => {
  return (
    '₱ ' +
    money?.toLocaleString('en-US', {
      maximumFractionDigits: 2,
      minimumFractionDigits: 2,
    })
  );
}

export const toUpperCase = (value: string) :string  => {
  if (value) {
    return value.toUpperCase();
  }
  return '';
};

export const computePrice = (price: number, qty: number): number => {
  const total = price * qty;
  return total;
};

export const getOrderDetail = (
  itemPrice: Array<{ item_price: Array<{ unit_id: number; price: number; unit: object }> }>,
  variations: Array<{ unit: number; count: number }>
): Array<{ unit_id: number; count: number; price: number,  unit_name: string;  }> => {
  const result: Array<{ unit_id: number; count: number; price: number; unit_name: string; }> = [];

  // Iterate over each variation to find the corresponding price
  variations.forEach(variation => {
      // Look through all itemPrice entries to find a matching unit_id
      for (const item of itemPrice) {
          const matchingPrice = item.unit_id === variation.unit;
          if (matchingPrice) {
            //find it in the result and add the count because it is already exist.
             const unitExist = result.find(v => v.unit_id === variation.unit)
             if(unitExist){
              unitExist.count += variation.count;
              return
             }
              // Add the result with the found price
              result.push({
                  unit_id: variation.unit,
                  count: parseInt(variation.count),
                  price: item.online_price,
                  unit_name: item.unit.name
              });
              break; // Exit the loop once a matching price is found
          }
      }
  });
  return result;
};

export const getStoreName = (stores: Array<{ store: object} >):string  => {
  return stores[0]?.store?.name
}

export function getLocation(): Promise<GeolocationPosition> {
  const timeoutVal = 10 * 1000 * 1000;
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      return reject(new Error("Geolocation is not supported by this browser."));
    }

    navigator.geolocation.getCurrentPosition(
      (position: GeolocationPosition) => resolve(position),
      (error: GeolocationPositionError) => reject(error),
      { enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
    );
  });
}
