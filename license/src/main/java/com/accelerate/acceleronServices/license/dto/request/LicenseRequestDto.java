package com.accelerate.acceleronServices.license.dto.request;


import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.validation.constraints.*;

@Data
@AllArgsConstructor
@NoArgsConstructor
@JsonIgnoreProperties(ignoreUnknown = true)
public class LicenseRequestDto {
    @NotNull
    public int id;

    @NotBlank
    public String license;

    @NotBlank
    public String machineUID;

    @NotBlank
    public String dateInstall;

    @NotBlank
    public String dateExpire;

    @NotNull
    public boolean isTrial;

    @NotBlank
    public String machineCustomName;

    @NotBlank
    public String branch;

    @NotBlank
    public String branchName;

    @NotBlank
    public String client;

    @NotNull
    public boolean isOnlineEnabled;

    @NotNull
    public boolean isActive;


}
