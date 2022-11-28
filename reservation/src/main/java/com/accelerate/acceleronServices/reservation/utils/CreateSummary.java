package com.accelerate.acceleronServices.reservation.utils;

import com.accelerate.acceleronServices.reservation.enums.ReservationStatusEnum;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.model.ReservationSummary;

import java.util.List;

public class CreateSummary {

    public ReservationSummary createReservationSummary(List<ReservationEntity> reservationEntityList){
        ReservationSummary reservationSummary = new ReservationSummary();
        for(ReservationEntity entity:reservationEntityList){
            int status = entity.getStatus();
            if(status == ReservationStatusEnum.CREATED.getValue()){
                reservationSummary.setCreated(reservationSummary.getCreated()+1);
            } else if (status == ReservationStatusEnum.SEATED.getValue()) {
                reservationSummary.setSeated(reservationSummary.getSeated()+1);
            } else if (status == ReservationStatusEnum.COMPLETED.getValue()) {
                reservationSummary.setCompleted(reservationSummary.getCompleted()+1);
            } else {
                reservationSummary.setCancelled(reservationSummary.getCancelled()+1);
            }
        }
        reservationSummary.setTotal();
        return reservationSummary;
    }
}
