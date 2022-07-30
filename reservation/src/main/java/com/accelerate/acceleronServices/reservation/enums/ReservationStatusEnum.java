package com.accelerate.acceleronServices.reservation.enums;

public enum ReservationStatusEnum {
    CREATED(0),
    SEATED(1),
    COMPLETED(2),
    CANCELLED(5);


    private final int value;

    ReservationStatusEnum(int value){
        this.value = value;
    }

    public int getValue(){
        return this.value;
    }




}
