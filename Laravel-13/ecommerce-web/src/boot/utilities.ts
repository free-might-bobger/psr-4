
import moment from 'moment';

export const getAge = (birthDate: string): number => {
  return moment().diff(moment(birthDate, 'YYYYMMDD'), 'years');
};

export const addComma = (val: object) => {
  if (val) {
    return val + ',';
  }
};

interface PriceItem {
  online_price?: number;
  price?: number;
}

export const getPriceRange = (priceArray: Array<PriceItem>) => {
  if (!priceArray || priceArray.length === 0) {
    return '';
  }
  // Extract online prices from the array (fallback to price if online_price doesn't exist)
  const prices = priceArray.map((item) => item.online_price ?? item.price ?? 0).filter(price => price > 0);

  if (prices.length === 0) {
    return '';
  }

  // Find the minimum and maximum prices
  const minPrice = Math.min(...prices);
  const maxPrice = Math.max(...prices);

  if (prices.length > 1) {
    return `₱ ${minPrice.toFixed(2)} - ₱${maxPrice.toFixed(2)}`;
  }
  return `₱ ${minPrice.toFixed(2)}`;
};

export const capitalizeWord = (string: string): string => {
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
  itemPrice: Array<{ unit_id: number; online_price?: number; price: number; unit: { id: number; name: string } }>,
  variations: Array<{ unit: number; count: number }>
): Array<{ unit_id: number; count: number; price: number; unit_name: string; }> => {
  const result: Array<{ unit_id: number; count: number; price: number; unit_name: string; }> = [];

  // Iterate over each variation to find the corresponding price
  variations.forEach(variation => {
      // Look through all itemPrice entries to find a matching unit_id
      for (const priceItem of itemPrice) {
          const matchingPrice = priceItem.unit_id === variation.unit;
          if (matchingPrice) {
            //find it in the result and add the count because it is already exist.
             const unitExist = result.find(v => v.unit_id === variation.unit)
             if(unitExist){
              unitExist.count += variation.count;
              return
             }
              // Add the result with the found price (use online_price if available, otherwise price)
              result.push({
                  unit_id: variation.unit,
                  count: variation.count,
                  price: priceItem.online_price ?? priceItem.price,
                  unit_name: priceItem.unit.name
              });
              return; // Exit the loop once a matching price is found
          }
      }
  });
  return result;
};

interface Store {
  name: string;
}

export const getStoreName = (stores: Array<{ store: Store} >):string  => {
  return stores[0]?.store?.name
}

export function getLocation(): Promise<GeolocationPosition> {
  const timeoutVal = 10 * 1000 * 1000;
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      return reject(new Error('Geolocation is not supported by this browser.'));
    }

    navigator.geolocation.getCurrentPosition(
      (position: GeolocationPosition) => resolve(position),
      (error: GeolocationPositionError) => reject(error),
      { enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
    );
  });
}

export function watchLocation(
  onSuccess: (position: GeolocationPosition) => void,
  onError?: (error: GeolocationPositionError) => void
): number {
  const timeoutVal = 10 * 1000 * 1000;
  if (!navigator.geolocation) {
    if (onError) {
      onError(new Error('Geolocation is not supported by this browser.') as unknown as GeolocationPositionError);
    }
    return -1;
  }

  return navigator.geolocation.watchPosition(
    onSuccess,
    onError,
    { enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
  );
}

export function clearWatch(watchId: number): void {
  if (navigator.geolocation && watchId !== -1) {
    navigator.geolocation.clearWatch(watchId);
  }
}


