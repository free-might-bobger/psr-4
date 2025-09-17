export type studentStatus = 'AOI' | 'DIMM' | 'FAN';

export enum LoginType {
  LOGIN = 'login',
  LOGOUT = 'logout',
}

export const MAX_RADIUS = 5;

export const GOOGLE_APIS_GEOCODE =
  'https://maps.googleapis.com/maps/api/geocode/json?';
export const GOOGLE_MAP_API_KEY = 'AIzaSyBQqI-E0G9CJgg2Ew8sf3YRX3GAEgFBA3o';
export const GOOGLE_MAP_ID = 'c94386de82bcb0e1';
export const DEFAULT_LAT = 10.333333;
export const DEFAULT_LNG = 123.933334;

export enum DELIVERY_TYPE {
  DELIVER = 1,
  PICKUP = 2,
}

export enum ERROR_CODE{
  UNAUTHORIZED = 401
}
