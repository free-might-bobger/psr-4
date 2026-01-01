import { isEmailUnique } from './axios-call';
import { ErrorValidationInterface } from './interfaces';

export const isValidMobileNumber = (mobile: string): boolean => {
  const mobileNumberPattern = /^\d{10}$/;
  return mobileNumberPattern.test(mobile);
}

export const validateRequired = (val: object) => {
  if (val) {
    return true;
  }
  return false;
};

export const validateUniqueEmail = async (email: string): Promise<ErrorValidationInterface> => {
  
  const isUnique = await isEmailUnique({
    email: email,
  });
  if (!isUnique) {
    return {
      message: 'Email already exist.',
      status: false,
    };
  }

  return {
    message: '',
    status: true,
  };
};

export const validateValidEmail = (email: string): ErrorValidationInterface => {
  if (!/[a-z0-9]+@[a-z]+\.[a-z]{2,3}/.test(email)) {
    return {
      message: 'It must be an email format.',
      status: false,
    };
  }
  return {
    message: '',
    status: true
  }
}


