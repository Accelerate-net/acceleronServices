package com.accelerate.acceleronServices.common;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import org.modelmapper.ModelMapper;
import org.springframework.beans.factory.annotation.Autowired;

public class Utils {
    public static boolean isValidUserName(String name){
    if(name == null || name.length() == 0){
        return false;
    }
    return true;
    }

    public static boolean isValidMobileNo(String mobileNo){
        if(mobileNo == null || mobileNo.length() < 10){
            return false;
        }
        return true;
    }

    public static boolean isValidOutlet(String outlet){
        if(outlet == null || outlet.length() == 0){
            return false;
        }
        return true;
    }

    public static boolean isValidDate(String date){
        if(date == null || date.length() == 0){
            return false;
        }
        return true;
    }

    public static boolean isValidtime(String time){
        if(time == null || time.length() == 0){
            return false;
        }
        return true;
    }

    public static boolean isValidCount(String count){
        if(count == null || Integer.parseInt(count) > 30){
            return false;
        }
        return true;
    }

}
