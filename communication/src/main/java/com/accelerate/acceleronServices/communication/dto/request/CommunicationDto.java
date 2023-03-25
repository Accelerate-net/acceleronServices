package com.accelerate.acceleronServices.communication.dto.request;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Pattern;

@Data
@AllArgsConstructor
@NoArgsConstructor
@JsonIgnoreProperties(ignoreUnknown = true)
public class CommunicationDto {

    @NotNull
    public int id;

    @NotBlank
    public String userName;

    public String userEmail;

    @Pattern(regexp = "^\\d{10}$", message = "Invalid Mobile Number")
    public String mobileNo;




}
