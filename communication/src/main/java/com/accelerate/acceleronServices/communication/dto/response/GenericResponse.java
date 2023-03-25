package com.accelerate.acceleronServices.communication.dto.response;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;

@Data
@AllArgsConstructor
@Builder
public class GenericResponse {
    String status;
}
