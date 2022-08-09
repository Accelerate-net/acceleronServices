package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.enums.ReservationStatusEnum;
import com.accelerate.acceleronServices.reservation.enums.StatusTextEnum;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.model.ReservationSummary;
import com.accelerate.acceleronServices.reservation.repository.ReservationRepository;
import com.accelerate.acceleronServices.reservation.utils.CreateSummary;
import com.accelerate.acceleronServices.reservation.utils.EntityDtoConversion;
import com.accelerate.acceleronServices.reservation.utils.ResponseMessage;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import javax.persistence.EntityNotFoundException;
import java.util.List;

@Service
@AllArgsConstructor
@Slf4j
public class ReservationRequestServiceImpl implements ReservationRequestService {

    @Autowired
    private ReservationRepository reservationRepository;

    @Autowired
    private EntityDtoConversion entityDtoConversion;

    @Autowired
    private CreateSummary createSummary;



    @Override
    @Transactional
    public ApiResponse<GenericResponse> makeReservation(ReservationRequestDto request) {

        ReservationEntity reservationEntity = entityDtoConversion.convertToEntity(request);
        reservationRepository.save(reservationEntity);
        ApiResponse<GenericResponse> response = new ApiResponse<>(true,HttpStatus.CREATED.value(),ResponseMessage.success,
                new GenericResponse(StatusTextEnum.SUCCESS.value()));

        return response;
    }
    @Override
    public List<ReservationEntity> getAllReservation(Integer limit, Integer skip){

        if (limit == null && skip == null) {
            return reservationRepository.findAll();  //return all table rows

        }
        if (limit != null && skip == null) {
            return reservationRepository.findAllById(limit, 0);  //returns first x rows where x=limit
        }
        return reservationRepository.findAllById(limit, skip);
    }



    @Override
    public ReservationEntity getReservationById(int id) {
        ReservationEntity reservationEntity = reservationRepository.findById(id);
        if (reservationEntity != null) {
            return reservationEntity;
        } else {
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }
    @Override
    public ApiResponse<GenericResponse> deleteReservation(int id) {

        if(reservationRepository.DeleteById(id)==1){
            ApiResponse<GenericResponse> response = new ApiResponse<>(true,HttpStatus.OK.value(), ResponseMessage.success,
                    new GenericResponse(StatusTextEnum.SUCCESS.value()));
            return response;
        }
        else{//if the id is not present
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse<GenericResponse> updateReservation(int id, ReservationRequestDto request) {

        ReservationEntity requestEntity = entityDtoConversion.convertToEntity(request);
        ReservationEntity reservationEntity = reservationRepository.findById(id);

        if(reservationEntity != null){
            requestEntity.setId(reservationEntity.getId());
            reservationRepository.save(requestEntity);
            return new ApiResponse<>(true,HttpStatus.OK.value(),ResponseMessage.success,
                    new GenericResponse(StatusTextEnum.SUCCESS.value()));
        }
        else{ //if the id is not present
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public List<ReservationEntity> getAllReservationBySearch(String search, Integer limit) {
        if(limit==null){
            limit=10;
        }
        return reservationRepository.findByGivenString(search,limit,0);
    }

    @Override
    public ApiResponse<GenericResponse> updateStatus(int id, String status) {
        if(reservationRepository.updateStatus(id,ReservationStatusEnum.valueOf(status).getValue()) == 1){
            ApiResponse<GenericResponse> response = new ApiResponse<>(true,HttpStatus.OK.value(), ResponseMessage.success,
                    new GenericResponse(StatusTextEnum.SUCCESS.value()));
            return response;
        }
        else{//if the id is not present
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ReservationSummary getReservationSummaryByUserId(String userId) {
        List<ReservationEntity> reservationEntities =  reservationRepository.findByUserId(userId);
        if (reservationEntities!=null){
            return createSummary.createReservationSummary(reservationEntities);
        }else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ReservationSummary getReservationSummaryPerBranch(String outlet, String date) {
        List<ReservationEntity> reservationEntities = reservationRepository.getReservationSummaryPerBranch(outlet, date);
        ReservationSummary reservationSummary = createSummary.createReservationSummary(reservationEntities);
        return reservationSummary;
    }

}
