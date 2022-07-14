package com.accelerate.acceleronServices.reservation.dto.request;

import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.validation.constraints.Max;
import javax.validation.constraints.NotBlank;
import javax.validation.constraints.Pattern;
import java.util.Objects;

@Data
@AllArgsConstructor
@NoArgsConstructor
@JsonIgnoreProperties(ignoreUnknown = true)
public class ReservationRequestDto {
    public String channel;

    @NotBlank(message = "userName cannot be empty")
    public String userName;

    public String userEmail;

    @Pattern(regexp = "^\\d{10}$", message = "Invalid Mobile Number")
    public String mobileNo;

    @NotBlank(message = "outlet cannot be empty")
    public String outlet;

    @NotBlank(message = "date cannot be empty")
    public String date;

    @NotBlank(message = "time cannot be empty")
    public String time;


    @Max(value = 30, message = "Maximum seats we can reserve at a time is 30. Contact us for Party Arrangements")
    public int count;
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
