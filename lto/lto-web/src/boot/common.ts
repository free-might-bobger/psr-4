import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';

const { menuIds, profile } = storeToRefs(useUserStore());

export const queryString = (query: object) => {
  const newQuery = removeEmptyValue(query);
  if (newQuery) {
    return Object.keys(newQuery)
      .map((key) => {
        const queryToString = newQuery[key].toString();
        return (
          encodeURIComponent(key) + '=' + encodeURIComponent(queryToString)
        );
      })
      .join('&');
  }
  return '';
};

export const removeEmptyValue = (
  obj: object
): Record<string, object> | null => {
  if (obj) {
    return Object.entries(obj).reduce(
      (a: Record<string, object>, [k, v]) => (v ? ((a[k] = v), a) : a),
      {}
    );
  }
  return null;
};


interface Errors {
  errors: object[];
}
export const errorToString = (errors: Errors): string | undefined => {
  const entries = Object.entries(errors);
  if (entries.length > 0) {
    let string = '';
    entries[0][1].forEach((value: string) => {
      string += value;
    });
    return string;
  }
};

interface MenuAllChildren {
  all_children: object[];
}
export const menuAllChildren = (menu: MenuAllChildren) => {
  const allChildren = menu.all_children;
  if (allChildren) {
    if (menu.all_children.length > 0) {
      return true;
    }
  }
  return false;
};

export const hasAccessToMenu = (menuId: number): boolean => {
  const accessMenus = menuIds.value.indexOf(menuId);
  if (accessMenus) {
    return true;
  }
  return false;
};

export async function urltoFile(
  url: string,
  filename: string,
  mimeType: string
): Promise<File> {
  return fetch(url)
    .then((res) => res.arrayBuffer())
    .then((buf) => new File([buf], filename, { type: mimeType }));
}

export function getPrimaryImageUrl(): string {
  if (profile.value.images.length > 0) {
    const image = profile.value.images.find((v) => v.is_primary === 1);
    if (image) {
      return image.path_url;
    }
  }

  return '';
}

export function capitalizeFirstLetter(string: string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

export function truncateString(str: string, num = 30): string {
  if(str===undefined) return ''
  if (str.length <= num) {
    return str;
  }
  return str.slice(0, num) + "...";
}

export function formatMoney(actualNumber: any) {
  return '₱ ' + actualNumber.toLocaleString('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 2 })
}

export function decimalThousandSeparator(value: number): number|string {
  if (value) {
    return currency() + ' ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&, ')
  }
  return 0.00

}

export function currency() {
  return '₱'
}

type GroupBy<T> = {
  [key: string]: T[];
};

/**
 * How to use groupBy(localResult.value.orders, 'store_id');
 * @param array 
 * @param key 
 * @returns 
 */
export function groupBy<T>(array: T[], key: keyof T): GroupBy<T> {
  return array.reduce((result, currentValue) => {
    const groupKey = String(currentValue[key]);
    if (!result[groupKey]) {
      result[groupKey] = [];
    }
    result[groupKey].push(currentValue);
    return result;
  }, {} as GroupBy<T>);
}

/**
 * NOTES; { mode: 'no-cors'} no image for development
 */
export const getFileFromUrl = async (url: string, name: string, defaultType = 'image/jpeg') => {
  const response = await fetch(url);
  const data = await response.blob();
  return new File([data], name, {
    type: data.type || defaultType,
  });
}


