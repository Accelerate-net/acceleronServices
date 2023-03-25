package com.accelerate.acceleronServices.user.enums;

public enum StatusTextEnum {
    SUCCESS("success"),
    FAILURE("failure");

    private final String value;

    StatusTextEnum(String value) {
        this.value = value;
    }

    public String value() {
        return this.value;
    }
}
