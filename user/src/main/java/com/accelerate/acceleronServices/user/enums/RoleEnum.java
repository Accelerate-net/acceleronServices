package com.accelerate.acceleronServices.user.enums;

public enum RoleEnum {
    USER(0),
    ADMIN(1),
    SUPER_ADMIN(2);


    private final int value;

    RoleEnum(int value){
        this.value = value;
    }

    public int getValue(){
        return this.value;
    }




}
