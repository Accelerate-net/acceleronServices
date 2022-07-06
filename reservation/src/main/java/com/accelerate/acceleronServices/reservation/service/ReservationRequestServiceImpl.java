package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.enums.StatusTextEnum;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.repository.ReservationRepository;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

@Service
@AllArgsConstructor
@Slf4j
public class ReservationRequestServiceImpl implements ReservationRequestService {

    @Autowired
    private ReservationRepository reservationRepository;

    @Override
    @Transactional
    public ApiResponse<GenericResponse> makeReservation(ReservationDto request) {
        reservationRepository.makeReservation(request.getStamp(),request.getMobileNo(), request.getUserName(),
                request.getUserEmail(), request.getOutlet(), request.getChannel(), request.getDate(),
                request.getTime(), request.getCount(), request.getComments(), request.getIsBirthday(), request.getIsAnniversary());

        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage(StatusTextEnum.SUCCESS.value());
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }

    @Override
    public List<ReservationEntity> getAllReservation(String search, Integer limit, Integer skip){

        if(search==null) {
            if (limit == null && skip == null) {
                return reservationRepository.findAll();
            }
            if (limit != null && skip == null) {
                return reservationRepository.findAllById(limit, 0);
            }
            return reservationRepository.findAllById(limit, limit);
        }
        else{
            if(skip==null){
                skip=0;
            }
            if(limit==null){
                limit=10;
            }
            return reservationRepository.findByGivenString(search,limit,0);
        }
    }
    @Override
    public Optional<ReservationEntity> getReservationById(int id){
        return reservationRepository.findById(id);
    }

    @Override
    public ApiResponse<GenericResponse> deleteReservation(int id) {
        reservationRepository.deleteById(id);


        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage("Deleted");
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }

    /*
    @Override
    public ApiResponse<GenericResponse> updateReservation(ReservationDto request, int id) {
        reservationRepository.updateReservation(request.getName(), request.getMobileNo());

        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage("Updated your reservation.");
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }
     */
}
