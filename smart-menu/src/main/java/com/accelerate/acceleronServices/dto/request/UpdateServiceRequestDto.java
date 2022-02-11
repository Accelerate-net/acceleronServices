package com.accelerate.acceleronServices.dto.request;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Data
@AllArgsConstructor
@NoArgsConstructor
public class UpdateServiceRequestDto {
    public int requestId;
    public int status;
}
