package com.accelerate.acceleronServices.reservation.dto.request;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.Objects;

@Data
@AllArgsConstructor
@NoArgsConstructor
public class ReservationDto {
    public String channel;
    public String userName;
    public String userEmail;
    public String mobileNo;
    public String outlet;
    public String date;
    public String time;
    public String count;
    public String comments;
    public String isAnniversary;
    public String isBirthday;


    public String getStamp(){
        return this.getDate().replace("-","");
    }

    public String getComments(){
        if(this.comments == null){
            return "";
        }
        else{return this.comments;}
    }


    public int getIsBirthday(){
        if(Objects.equals(this.isBirthday, "Yes")){
            return 1;
        }
        return 0;
    }

    public int getIsAnniversary(){
        if(Objects.equals(this.isAnniversary, "Yes")){
            return 1;
        }
        return 0;
    }






}
